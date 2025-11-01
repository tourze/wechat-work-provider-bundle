<?php

namespace WechatWorkProviderBundle\Tests\Request\License;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatWorkProviderBundle\Request\License\ActiveAccountRequest;

/**
 * @internal
 */
#[CoversClass(ActiveAccountRequest::class)]
final class ActiveAccountRequestTest extends RequestTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testRequestCanBeInstantiated(): void
    {
        $request = new ActiveAccountRequest();
        $this->assertInstanceOf(ActiveAccountRequest::class, $request);
    }
}
