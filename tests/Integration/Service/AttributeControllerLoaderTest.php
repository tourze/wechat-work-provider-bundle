<?php

namespace WechatWorkProviderBundle\Tests\Integration\Service;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Service\AttributeControllerLoader;

class AttributeControllerLoaderTest extends TestCase
{
    public function testServiceCanBeInstantiated(): void
    {
        $service = new AttributeControllerLoader();
        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
    }
}