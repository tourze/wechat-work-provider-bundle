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
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Entity\ProviderServerMessage;
use WechatWorkProviderBundle\Entity\Suite;
use WechatWorkProviderBundle\Entity\SuiteServerMessage;
use WechatWorkProviderBundle\Event\CorpServerMessageResponseEvent;
use WechatWorkProviderBundle\LegacyApi\WXBizMsgCrypt;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;
use WechatWorkProviderBundle\Repository\CorpServerMessageRepository;
use WechatWorkProviderBundle\Repository\ProviderServerMessageRepository;
use WechatWorkProviderBundle\Repository\SuiteRepository;
use WechatWorkProviderBundle\Repository\SuiteServerMessageRepository;

class ServerController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    /**
     * 服务商信息回调
     *
     * @see https://developer.work.weixin.qq.com/document/path/97172
     */
    #[Route('/wechat-work-provider/server/provider/{id}')]
    public function provider(
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

    /**
     * 应用回调
     *
     * @see https://developer.work.weixin.qq.com/document/path/90600
     */
    #[Route('/wechat-work-provider/server/suite/{id}')]
    public function suite(
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

    #[Route('/wechat-work-provider/server/start/{corpId}')]
    public function start(
        string $corpId,
        Request $request,
        LoggerInterface $logger,
        AuthCorpRepository $authCorpRepository,
        CorpServerMessageRepository $messageRepository,
        EventDispatcherInterface $eventDispatcher,
    ): Response {
        $authCorp = $authCorpRepository->findOneBy(['id' => $corpId]);
        if (!$authCorp) {
            $authCorp = $authCorpRepository->findOneBy(['corpId' => $corpId]);
        }
        if (!$authCorp) {
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

        $logger->info('接受到代开发回调', [
            'post' => $sMsg,
            'xml' => $xml,
            'res' => $res,
            'arr' => $arr,
        ]);

        $message = new CorpServerMessage();
        $message->setRawData($arr);

        if (isset($arr['CreateTime'])) {
            $message->setCreateTime($arr['CreateTime']);
        }
        if (isset($arr['ToUserName'])) {
            $message->setToUserName($arr['ToUserName']);
        }
        if (isset($arr['FromUserName'])) {
            $message->setFromUserName($arr['FromUserName']);
        }
        if (isset($arr['MsgType'])) {
            $message->setMsgType($arr['MsgType']);
        }
        if (isset($arr['Event'])) {
            $message->setEvent($arr['Event']);
        }
        if (isset($arr['ChangeType'])) {
            $message->setChangeType($arr['ChangeType']);
        }
        if (isset($arr['UserID'])) {
            $message->setUserId($arr['UserID']);
        }
        if (isset($arr['ExternalUserID'])) {
            $message->setExternalUserId($arr['ExternalUserID']);
        }
        if (isset($arr['WelcomeCode'])) {
            $message->setWelcomeCode($arr['WelcomeCode']);
        }
        if (isset($arr['ChatId'])) {
            $message->setChatId($arr['ChatId']);
        }
        if (isset($arr['UpdateDetail'])) {
            $message->setUpdateDetail($arr['UpdateDetail']);
        }
        if (isset($arr['JoinScene'])) {
            $message->setJoinScene($arr['JoinScene']);
        }
        if (isset($arr['MemChangeCnt'])) {
            $message->setMemChangeCnt($arr['MemChangeCnt']);
        }
        if (isset($arr['QuitScene'])) {
            $message->setQuitScene($arr['QuitScene']);
        }
        if (isset($arr['State'])) {
            $message->setState($arr['State']);
        }

        // TODO 需要手动处理这里的save逻辑
        try {
            $this->entityManager->persist($message);
            $this->entityManager->flush();
        } catch (\Throwable $exception) {
            $logger->error('保存代开发回调日志失败', [
                'arr' => $arr,
                'exception' => $exception,
            ]);
        }

        $event = new CorpServerMessageResponseEvent();
        $event->setMessage($message);
        $event->setAuthCorp($authCorp);
        $eventDispatcher->dispatch($event);

        return new Response($message->getResponse() ? $serializer->encode($message->getResponse(), 'xml') : 'success');
    }
}
