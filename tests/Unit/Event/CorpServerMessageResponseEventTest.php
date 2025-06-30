<?php

namespace WechatWorkProviderBundle\Tests\Unit\Event;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Entity\CorpServerMessage;
use WechatWorkProviderBundle\Event\CorpServerMessageResponseEvent;

class CorpServerMessageResponseEventTest extends TestCase
{
    public function testEventCanBeInstantiated(): void
    {
        $message = $this->createMock(CorpServerMessage::class);
        $authCorp = $this->createMock(\WechatWorkProviderBundle\Entity\AuthCorp::class);
        $message->method('getAuthCorp')->willReturn($authCorp);
        
        $event = new CorpServerMessageResponseEvent($message);
        $this->assertInstanceOf(CorpServerMessageResponseEvent::class, $event);
    }
}