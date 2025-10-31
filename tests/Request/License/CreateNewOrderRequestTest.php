<?php

namespace WechatWorkProviderBundle\Tests\Request\License;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatWorkProviderBundle\Request\License\CreateNewOrderRequest;

/**
 * @internal
 */
#[CoversClass(CreateNewOrderRequest::class)]
final class CreateNewOrderRequestTest extends RequestTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testRequestCanBeInstantiated(): void
    {
        $request = new CreateNewOrderRequest();
        $this->assertInstanceOf(CreateNewOrderRequest::class, $request);
    }
}
