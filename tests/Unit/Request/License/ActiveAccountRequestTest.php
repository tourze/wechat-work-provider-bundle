<?php

namespace WechatWorkProviderBundle\Tests\Unit\Request\License;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Request\License\ActiveAccountRequest;

class ActiveAccountRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new ActiveAccountRequest();
        $this->assertInstanceOf(ActiveAccountRequest::class, $request);
    }
}