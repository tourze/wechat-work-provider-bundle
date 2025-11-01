<?php

namespace WechatWorkProviderBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatWorkProviderBundle\Request\GetSuiteTokenRequest;

/**
 * @internal
 */
#[CoversClass(GetSuiteTokenRequest::class)]
final class GetSuiteTokenRequestTest extends RequestTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testRequestCanBeInstantiated(): void
    {
        $request = new GetSuiteTokenRequest();
        $this->assertInstanceOf(GetSuiteTokenRequest::class, $request);
    }
}
