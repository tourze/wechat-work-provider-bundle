<?php

namespace WechatWorkProviderBundle\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use WechatWorkProviderBundle\Request\WithSuiteRequest;

class WithSuiteRequestTest extends TestCase
{
    public function testClassIsAbstract(): void
    {
        $reflection = new ReflectionClass(WithSuiteRequest::class);
        $this->assertTrue($reflection->isAbstract());
    }
}