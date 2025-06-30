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

class SuiteCallbackController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    /**
     * 应用回调
     *
     * @see https://developer.work.weixin.qq.com/document/path/90600
     */
    #[Route(path: '/wechat-work-provider/server/suite/{id}')]
    public function __invoke(
        Suite $suite,
        Request $request,
        LoggerInterface $logger,
        SuiteRepository $suiteRepository,
        SuiteServerMessageRepository $messageRepository,
    ): Response {
        if ('GET' === $request->getMethod()) {
            $wxcpt = new WXBizMsgCrypt($suite->getToken(), $suite->getEncodingAesKey(), $suite->getProvider()->getCorpId());
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
            throw new HttpException(500, '校验不通过');
        }

        $wxcpt = new WXBizMsgCrypt($suite->getToken(), $suite->getEncodingAesKey(), $suite->getSuiteId());

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

        $logger->info('接受到应用回调', [
            'post' => $sMsg,
            'xml' => $xml,
            'res' => $res,
            'msg' => $msg,
        ]);

        $message = new SuiteServerMessage();
        $message->setSuite($suite);
        $message->setRawData($xml);
        $message->setContext($msg);
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return new Response('success');
    }
}