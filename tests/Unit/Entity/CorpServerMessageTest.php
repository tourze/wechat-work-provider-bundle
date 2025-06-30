<?php

namespace WechatWorkProviderBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Entity\CorpServerMessage;

class CorpServerMessageTest extends TestCase
{
    public function testEntityCanBeInstantiated(): void
    {
        $entity = new CorpServerMessage();
        $this->assertInstanceOf(CorpServerMessage::class, $entity);
    }
}