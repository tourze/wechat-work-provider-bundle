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
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Entity\CorpServerMessage;
use WechatWorkProviderBundle\Event\CorpServerMessageResponseEvent;
use WechatWorkProviderBundle\LegacyApi\WXBizMsgCrypt;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;
use WechatWorkProviderBundle\Repository\CorpServerMessageRepository;

final class CorpCallbackController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route(path: '/wechat-work-provider/server/start/{corpId}', methods: ['GET', 'POST'])]
    public function __invoke(
        string $corpId,
        Request $request,
        LoggerInterface $logger,
        AuthCorpRepository $authCorpRepository,
        CorpServerMessageRepository $messageRepository,
        EventDispatcherInterface $eventDispatcher,
    ): Response {
        $authCorp = $this->findAuthCorp($corpId, $authCorpRepository);
        $wxcpt = $this->createWxCrypt($authCorp);

        if ('GET' === $request->getMethod()) {
            return $this->handleGetRequest($request, $wxcpt);
        }

        return $this->handlePostRequest($request, $authCorp, $wxcpt, $logger, $eventDispatcher);
    }

    private function findAuthCorp(string $corpId, AuthCorpRepository $authCorpRepository): AuthCorp
    {
        $authCorp = $authCorpRepository->findOneBy(['id' => $corpId]);
        if (null === $authCorp) {
            $authCorp = $authCorpRepository->findOneBy(['corpId' => $corpId]);
        }
        if (null === $authCorp) {
            throw new HttpException(500, '找不到授权企业');
        }

        return $authCorp;
    }

    private function createWxCrypt(AuthCorp $authCorp): WXBizMsgCrypt
    {
        $token = $authCorp->getToken() ?? throw new HttpException(500, '授权企业Token不能为空');
        $encodingAesKey = $authCorp->getEncodingAesKey() ?? throw new HttpException(500, '授权企业EncodingAesKey不能为空');
        $corpId = $authCorp->getCorpId() ?? throw new HttpException(500, '授权企业ID不能为空');

        return new WXBizMsgCrypt($token, $encodingAesKey, $corpId);
    }

    private function handleGetRequest(Request $request, WXBizMsgCrypt $wxcpt): Response
    {
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
        throw new HttpException(500, (string) $result->errorCode);
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

    private function handlePostRequest(
        Request $request,
        AuthCorp $authCorp,
        WXBizMsgCrypt $wxcpt,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher,
    ): Response {
        $xml = $request->getContent();
        $decryptedData = $this->decryptMessage($request, $wxcpt, $xml);

        $logData = $decryptedData['logData'];
        if (is_array($logData)) {
            $logger->info('接受到企业回调', $logData);
        }

        $context = $decryptedData['context'];
        $validContext = $this->validateContext($context);
        $message = $this->createServerMessage($authCorp, $xml, $validContext);
        $responseData = $this->dispatchEvent($message, $eventDispatcher);

        if (0 === count($responseData)) {
            return new Response('success');
        }

        return $this->createEncryptedResponse($request, $wxcpt, $responseData);
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
            throw new HttpException(500, '企业回调解密失败: ' . (string) $result->errorCode);
        }

        $sMsg = $result->data;
        $encoder = new XmlEncoder();
        $serializer = new Serializer([], [$encoder]);
        $arr = '' !== $sMsg ? $serializer->decode($sMsg, 'xml') : [];

        return [
            'context' => is_array($arr) ? $arr : null,
            'logData' => [
                'post' => $sMsg,
                'xml' => $xml,
                'res' => $result->errorCode,
                'arr' => $arr,
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
    private function createServerMessage(AuthCorp $authCorp, string $xml, ?array $context): CorpServerMessage
    {
        $message = new CorpServerMessage();
        $message->setAuthCorp($authCorp);
        $message->setRawData(['xml' => $xml]);
        $message->setContext($context);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }

    /**
     * @return array<string, mixed>
     */
    private function dispatchEvent(CorpServerMessage $message, EventDispatcherInterface $eventDispatcher): array
    {
        $event = new CorpServerMessageResponseEvent($message);
        $eventDispatcher->dispatch($event);

        return $event->getResponseData();
    }

    /**
     * @param array<string, mixed> $responseData
     */
    private function createEncryptedResponse(Request $request, WXBizMsgCrypt $wxcpt, array $responseData): Response
    {
        $encoder = new XmlEncoder();
        $serializer = new Serializer([], [$encoder]);
        $responseXml = $serializer->encode($responseData, 'xml', [
            'xml_root_node_name' => 'xml',
            'xml_encoding' => 'UTF-8',
        ]);

        $params = $this->getPostParams($request);

        $result = $wxcpt->EncryptMsg($responseXml, (int) $params['timestamp'], (string) $params['nonce']);
        if ($result->isError()) {
            throw new HttpException(500, '企业回调加密失败: ' . (string) $result->errorCode);
        }

        return new Response($result->data);
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
