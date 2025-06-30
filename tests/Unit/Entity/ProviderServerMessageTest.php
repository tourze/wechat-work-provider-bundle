<?php

namespace WechatWorkProviderBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Entity\ProviderServerMessage;

class ProviderServerMessageTest extends TestCase
{
    public function testEntityCanBeInstantiated(): void
    {
        $entity = new ProviderServerMessage();
        $this->assertInstanceOf(ProviderServerMessage::class, $entity);
    }
}