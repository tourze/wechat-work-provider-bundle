<?php

namespace WechatWorkProviderBundle\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Request\GetProviderTokenRequest;

class GetProviderTokenRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new GetProviderTokenRequest();
        $this->assertInstanceOf(GetProviderTokenRequest::class, $request);
    }
}