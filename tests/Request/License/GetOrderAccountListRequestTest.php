<?php

namespace WechatWorkProviderBundle\Tests\Request\License;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatWorkProviderBundle\Request\License\GetOrderAccountListRequest;

/**
 * @internal
 */
#[CoversClass(GetOrderAccountListRequest::class)]
final class GetOrderAccountListRequestTest extends RequestTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testRequestCanBeInstantiated(): void
    {
        $request = new GetOrderAccountListRequest();
        $this->assertInstanceOf(GetOrderAccountListRequest::class, $request);
    }
}
