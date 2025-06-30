<?php

namespace WechatWorkProviderBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Entity\SuiteServerMessage;

class SuiteServerMessageTest extends TestCase
{
    public function testEntityCanBeInstantiated(): void
    {
        $entity = new SuiteServerMessage();
        $this->assertInstanceOf(SuiteServerMessage::class, $entity);
    }
}