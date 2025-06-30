<?php

namespace WechatWorkProviderBundle\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use WechatWorkProviderBundle\Request\WithProviderRequest;

class WithProviderRequestTest extends TestCase
{
    public function testClassIsAbstract(): void
    {
        $reflection = new ReflectionClass(WithProviderRequest::class);
        $this->assertTrue($reflection->isAbstract());
    }
}