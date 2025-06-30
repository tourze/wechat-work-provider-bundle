<?php

namespace WechatWorkProviderBundle\Tests\Integration\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use WechatWorkProviderBundle\EventSubscriber\WechatWorkSubscriber;
use WechatWorkProviderBundle\Service\ProviderService;

class WechatWorkSubscriberTest extends TestCase
{
    public function testSubscriberCanBeInstantiated(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $providerService = $this->createMock(ProviderService::class);
        
        $subscriber = new WechatWorkSubscriber($eventDispatcher, $providerService);
        $this->assertInstanceOf(WechatWorkSubscriber::class, $subscriber);
    }
}