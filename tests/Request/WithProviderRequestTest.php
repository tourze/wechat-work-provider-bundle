<?php

namespace WechatWorkProviderBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatWorkProviderBundle\Request\WithProviderRequest;

/**
 * @internal
 */
#[CoversClass(WithProviderRequest::class)]
final class WithProviderRequestTest extends RequestTestCase
{
    public function testClassIsAbstract(): void
    {
        $reflection = new \ReflectionClass(WithProviderRequest::class);
        $this->assertTrue($reflection->isAbstract());
    }
}
