<?php

namespace WechatWorkProviderBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use WechatWorkProviderBundle\Event\CorpServerMessageResponseEvent;
use WechatWorkProviderBundle\Service\ProviderService;
use WechatWorkServerBundle\Entity\ServerMessage;
use WechatWorkServerBundle\Event\WechatWorkServerMessageRequestEvent;

class WechatWorkSubscriber
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ProviderService $providerService,
    ) {
    }

    /**
     * 把服务商收到的请求，同步一份到企业微信模块
     */
    #[AsEventListener]
    public function onCorpServerMessageResponse(CorpServerMessageResponseEvent $event): void
    {
        $corp = $this->providerService->syncAuthCorpToCorpAndAgent($event->getAuthCorp());
        $arr = $event->getMessage()->getRawData();

        $message = ServerMessage::createFromArray($arr);
        $message->setCorp($corp);
        $message->setAgent($corp->getAgents()->first());

        $nextEvent = new WechatWorkServerMessageRequestEvent();
        $nextEvent->setMessage($message);
        $this->eventDispatcher->dispatch($nextEvent);
    }
}
