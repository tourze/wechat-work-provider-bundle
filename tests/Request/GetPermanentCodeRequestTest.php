<?php

namespace WechatWorkProviderBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatWorkProviderBundle\Request\GetPermanentCodeRequest;

/**
 * @internal
 */
#[CoversClass(GetPermanentCodeRequest::class)]
final class GetPermanentCodeRequestTest extends RequestTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testRequestCanBeInstantiated(): void
    {
        $request = new GetPermanentCodeRequest();
        $this->assertInstanceOf(GetPermanentCodeRequest::class, $request);
    }
}
