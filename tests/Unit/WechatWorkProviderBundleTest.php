<?php

namespace WechatWorkProviderBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\WechatWorkProviderBundle;

class WechatWorkProviderBundleTest extends TestCase
{
    public function testBundleCanBeInstantiated(): void
    {
        $bundle = new WechatWorkProviderBundle();
        $this->assertInstanceOf(WechatWorkProviderBundle::class, $bundle);
    }
}