<?php

namespace WechatWorkProviderBundle\Tests\Unit\LegacyApi;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\LegacyApi\Prpcrypt;

class PrpcryptTest extends TestCase
{
    public function testClassCanBeInstantiated(): void
    {
        $prpcrypt = new Prpcrypt('test-key');
        $this->assertInstanceOf(Prpcrypt::class, $prpcrypt);
    }
}