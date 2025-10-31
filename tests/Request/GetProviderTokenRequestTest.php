<?php

namespace WechatWorkProviderBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatWorkProviderBundle\Request\GetProviderTokenRequest;

/**
 * @internal
 */
#[CoversClass(GetProviderTokenRequest::class)]
final class GetProviderTokenRequestTest extends RequestTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testRequestCanBeInstantiated(): void
    {
        $request = new GetProviderTokenRequest();
        $this->assertInstanceOf(GetProviderTokenRequest::class, $request);
    }
}
