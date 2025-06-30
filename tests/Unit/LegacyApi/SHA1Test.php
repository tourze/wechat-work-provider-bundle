<?php

namespace WechatWorkProviderBundle\Tests\Unit\LegacyApi;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\LegacyApi\SHA1;

class SHA1Test extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(SHA1::class));
    }
}