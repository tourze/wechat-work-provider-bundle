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
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Entity\ProviderServerMessage;
use WechatWorkProviderBundle\LegacyApi\WXBizMsgCrypt;
use WechatWorkProviderBundle\Repository\ProviderServerMessageRepository;

class ProviderCallbackController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    /**
     * 服务商信息回调
     *
     * @see https://developer.work.weixin.qq.com/document/path/97172
     */
    #[Route('/wechat-work-provider/server/provider/{id}')]
    public function __invoke(
        Provider $provider,
        Request $request,
        LoggerInterface $logger,
        ProviderServerMessageRepository $messageRepository,
    ): Response {
        $wxcpt = new WXBizMsgCrypt($provider->getToken(), $provider->getEncodingAesKey(), $provider->getCorpId());

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
        $msg = $serializer->decode($sMsg, 'xml');

        $logger->info('接受到服务商回调', [
            'post' => $sMsg,
            'xml' => $xml,
            'res' => $res,
            'msg' => $msg,
        ]);

        $message = new ProviderServerMessage();
        $message->setProvider($provider);
        $message->setRawData($xml);
        $message->setContext($msg);
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return new Response('success');
    }
}