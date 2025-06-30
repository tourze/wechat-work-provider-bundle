<?php

namespace WechatWorkProviderBundle\Tests\Unit\Request\License;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Request\License\CreateNewOrderRequest;

class CreateNewOrderRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new CreateNewOrderRequest();
        $this->assertInstanceOf(CreateNewOrderRequest::class, $request);
    }
}