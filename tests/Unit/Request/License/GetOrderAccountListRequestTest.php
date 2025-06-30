<?php

namespace WechatWorkProviderBundle\Tests\Unit\Request\License;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Request\License\GetOrderAccountListRequest;

class GetOrderAccountListRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new GetOrderAccountListRequest();
        $this->assertInstanceOf(GetOrderAccountListRequest::class, $request);
    }
}