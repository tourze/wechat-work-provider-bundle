<?php

namespace WechatWorkProviderBundle\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Request\GetPermanentCodeRequest;

class GetPermanentCodeRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new GetPermanentCodeRequest();
        $this->assertInstanceOf(GetPermanentCodeRequest::class, $request);
    }
}