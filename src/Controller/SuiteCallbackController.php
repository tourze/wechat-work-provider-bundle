<?php

namespace WechatWorkProviderBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use WechatWorkProviderBundle\Entity\Suite;
use WechatWorkProviderBundle\Entity\SuiteServerMessage;
use WechatWorkProviderBundle\LegacyApi\WXBizMsgCrypt;
use WechatWorkProviderBundle\Repository\SuiteRepository;
use WechatWorkProviderBundle\Repository\SuiteServerMessageRepository;

final class SuiteCallbackController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * 应用回调
     *
     * @see https://developer.work.weixin.qq.com/document/path/90600
     */
    #[Route(path: '/wechat-work-provider/server/suite/{id}', methods: ['GET', 'POST'])]
    public function __invoke(
        Suite $suite,
        Request $request,
        LoggerInterface $logger,
        SuiteRepository $suiteRepository,
        SuiteServerMessageRepository $messageRepository,
    ): Response {
        if ('GET' === $request->getMethod()) {
            return $this->handleGetRequest($request, $suite);
        }

        return $this->handlePostRequest($request, $suite, $logger);
    }

    private function handleGetRequest(Request $request, Suite $suite): Response
    {
        $wxcpt = $this->createGetWxCrypt($suite);
        $params = $this->validateGetParams($request);

        $result = $wxcpt->VerifyURL(
            $params['msgSignature'],
            $params['timestamp'],
            $params['nonce'],
            $params['echostr']
        );

        if ($result->isSuccess()) {
            return new Response($result->data);
        }
        throw new HttpException(500, '校验不通过: ' . (string) $result->errorCode);
    }

    private function createGetWxCrypt(Suite $suite): WXBizMsgCrypt
    {
        $provider = $suite->getProvider();
        if (null === $provider) {
            throw new HttpException(500, 'Suite provider not found');
        }

        $suiteToken = $suite->getToken() ?? throw new HttpException(500, '应用Token不能为空');
        $suiteEncodingAesKey = $suite->getEncodingAesKey() ?? throw new HttpException(500, '应用EncodingAesKey不能为空');
        $providerCorpId = $provider->getCorpId() ?? throw new HttpException(500, '服务商企业ID不能为空');

        return new WXBizMsgCrypt($suiteToken, $suiteEncodingAesKey, $providerCorpId);
    }

    /**
     * @return array<string, string>
     */
    private function validateGetParams(Request $request): array
    {
        $msgSignature = $request->query->get('msg_signature');
        $timestamp = $request->query->get('timestamp');
        $nonce = $request->query->get('nonce');
        $echostr = $request->query->get('echostr');

        if (!is_string($msgSignature)) {
            throw new HttpException(400, '缺少必要的查询参数: msg_signature');
        }
        if (!is_string($timestamp)) {
            throw new HttpException(400, '缺少必要的查询参数: timestamp');
        }
        if (!is_string($nonce)) {
            throw new HttpException(400, '缺少必要的查询参数: nonce');
        }
        if (!is_string($echostr)) {
            throw new HttpException(400, '缺少必要的查询参数: echostr');
        }

        return [
            'msgSignature' => $msgSignature,
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'echostr' => $echostr,
        ];
    }

    private function handlePostRequest(Request $request, Suite $suite, LoggerInterface $logger): Response
    {
        $wxcpt = $this->createPostWxCrypt($suite);
        $xml = $request->getContent();
        $decryptedData = $this->decryptMessage($request, $wxcpt, $xml);

        $logData = $decryptedData['logData'];
        if (is_array($logData)) {
            $logger->info('接受到应用回调', $logData);
        }

        // 如果解密失败，直接返回success避免重试
        if (isset($decryptedData['decryptError'])) {
            $logger->warning('解密失败，返回success避免重试', is_array($logData) ? $logData : []);

            return new Response('success');
        }

        $context = $this->validateContext($decryptedData['context']);
        $this->createServerMessage($suite, $xml, $context);

        return new Response('success');
    }

    private function createPostWxCrypt(Suite $suite): WXBizMsgCrypt
    {
        $postToken = $suite->getToken() ?? throw new HttpException(500, '应用Token不能为空');
        $postEncodingAesKey = $suite->getEncodingAesKey() ?? throw new HttpException(500, '应用EncodingAesKey不能为空');
        $suiteId = $suite->getSuiteId() ?? throw new HttpException(500, '应用ID不能为空');

        return new WXBizMsgCrypt($postToken, $postEncodingAesKey, $suiteId);
    }

    /**
     * @return array<string, mixed>
     */
    private function decryptMessage(Request $request, WXBizMsgCrypt $wxcpt, string $xml): array
    {
        $params = $this->getPostParams($request);

        $result = $wxcpt->DecryptMsg(
            (string) $params['msgSignature'],
            (int) $params['timestamp'],
            (string) $params['nonce'],
            $xml
        );

        if ($result->isError()) {
            // 解密失败时返回错误标记，不抛出异常
            return [
                'context' => null,
                'decryptError' => true,
                'logData' => [
                    'post' => '',
                    'xml' => $xml,
                    'res' => $result->errorCode,
                    'msg' => null,
                    'error' => '解密失败: ' . (string) $result->errorCode,
                ],
            ];
        }

        $sMsg = $result->data;
        $encoder = new XmlEncoder();
        $serializer = new Serializer([], [$encoder]);
        $msg = '' !== $sMsg ? $serializer->decode($sMsg, 'xml') : [];

        return [
            'context' => is_array($msg) ? $msg : null,
            'logData' => [
                'post' => $sMsg,
                'xml' => $xml,
                'res' => $result->errorCode,
                'msg' => $msg,
            ],
        ];
    }

    /**
     * @return array<string, string|int>
     */
    private function getPostParams(Request $request): array
    {
        $msgSignature = $request->query->get('msg_signature');
        $timestamp = $request->query->get('timestamp');
        $nonce = $request->query->get('nonce');

        return [
            'msgSignature' => is_string($msgSignature) ? $msgSignature : '',
            'timestamp' => is_numeric($timestamp) ? (int) $timestamp : 0,
            'nonce' => is_string($nonce) ? $nonce : '',
        ];
    }

    /**
     * @param array<string, mixed>|null $context
     */
    private function createServerMessage(Suite $suite, string $xml, ?array $context): void
    {
        $message = new SuiteServerMessage();
        $message->setSuite($suite);
        $message->setRawData($xml);
        $message->setContext($context);

        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    /**
     * @param mixed $context
     * @return array<string, mixed>|null
     */
    private function validateContext($context): ?array
    {
        if (!is_array($context)) {
            return null;
        }

        $result = [];
        foreach ($context as $key => $value) {
            if (is_string($key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
