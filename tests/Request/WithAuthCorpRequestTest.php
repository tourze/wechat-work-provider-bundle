<?php

namespace WechatWorkProviderBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatWorkProviderBundle\Request\WithAuthCorpRequest;

/**
 * @internal
 */
#[CoversClass(WithAuthCorpRequest::class)]
final class WithAuthCorpRequestTest extends RequestTestCase
{
    public function testClassIsAbstract(): void
    {
        $reflection = new \ReflectionClass(WithAuthCorpRequest::class);
        $this->assertTrue($reflection->isAbstract());
    }
}
