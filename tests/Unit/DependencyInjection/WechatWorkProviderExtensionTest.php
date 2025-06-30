<?php

namespace WechatWorkProviderBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\DependencyInjection\WechatWorkProviderExtension;

class WechatWorkProviderExtensionTest extends TestCase
{
    public function testExtensionCanBeInstantiated(): void
    {
        $extension = new WechatWorkProviderExtension();
        $this->assertInstanceOf(WechatWorkProviderExtension::class, $extension);
    }
}