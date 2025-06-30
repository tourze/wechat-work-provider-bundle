<?php

namespace WechatWorkProviderBundle\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Request\GetSuiteTokenRequest;

class GetSuiteTokenRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new GetSuiteTokenRequest();
        $this->assertInstanceOf(GetSuiteTokenRequest::class, $request);
    }
}