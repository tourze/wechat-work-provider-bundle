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
use WechatWorkProviderBundle\Entity\CorpServerMessage;
use WechatWorkProviderBundle\Event\CorpServerMessageResponseEvent;
use WechatWorkProviderBundle\LegacyApi\WXBizMsgCrypt;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;
use WechatWorkProviderBundle\Repository\CorpServerMessageRepository;

class CorpCallbackController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Route('/wechat-work-provider/server/start/{corpId}')]
    public function __invoke(
        string $corpId,
        Request $request,
        LoggerInterface $logger,
        AuthCorpRepository $authCorpRepository,
        CorpServerMessageRepository $messageRepository,
        EventDispatcherInterface $eventDispatcher,
    ): Response {
        $authCorp = $authCorpRepository->findOneBy(['id' => $corpId]);
        if ($authCorp === null) {
            $authCorp = $authCorpRepository->findOneBy(['corpId' => $corpId]);
        }
        if ($authCorp === null) {
            throw new HttpException(500, '找不到授权企业');
        }

        $wxcpt = new WXBizMsgCrypt($authCorp->getToken(), $authCorp->getEncodingAesKey(), $authCorp->getCorpId());

        if ('GET' === $request->getMethod()) {
            $errCode = $wxcpt->VerifyURL(
                $request->query->get('msg_signature'),
                $request->query->get('timestamp'),
                $request->query->get('nonce'),
                $request->query->get('echostr'),
                $sEchoStr
            );

            if (0 == $errCode) {
                return new Response($sEchoStr);
            }
            throw new HttpException(500, strval($errCode));
        }

        $xml = $request->getContent();

        $res = $wxcpt->DecryptMsg(
            $request->query->get('msg_signature'),
            $request->query->get('timestamp'),
            $request->query->get('nonce'),
            $xml,
            $sMsg,
        );

        $encoder = new XmlEncoder();
        $serializer = new Serializer([], [$encoder]);
        $arr = $serializer->decode($sMsg, 'xml');

        $logger->info('接受到企业回调', [
            'post' => $sMsg,
            'xml' => $xml,
            'res' => $res,
            'arr' => $arr,
        ]);

        $message = new CorpServerMessage();
        $message->setAuthCorp($authCorp);
        $message->setRawData(['xml' => $xml]);
        $message->setContext($arr);
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        $event = new CorpServerMessageResponseEvent($message);
        $eventDispatcher->dispatch($event);
        $responseData = $event->getResponseData();

        if (empty($responseData)) {
            return new Response('success');
        }

        $responseXml = $serializer->encode($responseData, 'xml', [
            'xml_root_node_name' => 'xml',
            'xml_encoding' => 'UTF-8',
        ]);

        $res = $wxcpt->EncryptMsg($responseXml, $request->query->get('timestamp'), $request->query->get('nonce'), $encryptMsg);
        if (0 !== $res) {
            throw new HttpException(500, '企业回调加密失败: ' . strval($res));
        }

        return new Response($encryptMsg);
    }
}