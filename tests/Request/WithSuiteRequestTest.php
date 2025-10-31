<?php

namespace WechatWorkProviderBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatWorkProviderBundle\Request\WithSuiteRequest;

/**
 * @internal
 */
#[CoversClass(WithSuiteRequest::class)]
final class WithSuiteRequestTest extends RequestTestCase
{
    public function testClassIsAbstract(): void
    {
        $reflection = new \ReflectionClass(WithSuiteRequest::class);
        $this->assertTrue($reflection->isAbstract());
    }
}
