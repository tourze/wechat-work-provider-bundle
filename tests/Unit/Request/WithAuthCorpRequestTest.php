<?php

namespace WechatWorkProviderBundle\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use WechatWorkProviderBundle\Request\WithAuthCorpRequest;

class WithAuthCorpRequestTest extends TestCase
{
    public function testClassIsAbstract(): void
    {
        $reflection = new ReflectionClass(WithAuthCorpRequest::class);
        $this->assertTrue($reflection->isAbstract());
    }
}