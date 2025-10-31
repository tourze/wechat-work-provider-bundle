<?php

namespace WechatWorkProviderBundle\Tests\Request\Batch;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatWorkProviderBundle\Request\Batch\UserIdToOpenUserIdRequest;

/**
 * @internal
 */
#[CoversClass(UserIdToOpenUserIdRequest::class)]
final class UserIdToOpenUserIdRequestTest extends RequestTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testRequestCanBeInstantiated(): void
    {
        $request = new UserIdToOpenUserIdRequest();
        $this->assertInstanceOf(UserIdToOpenUserIdRequest::class, $request);
    }
}
