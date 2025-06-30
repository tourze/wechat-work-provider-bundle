<?php

namespace WechatWorkProviderBundle\Tests\Unit\Request\Batch;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Request\Batch\UserIdToOpenUserIdRequest;

class UserIdToOpenUserIdRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new UserIdToOpenUserIdRequest();
        $this->assertInstanceOf(UserIdToOpenUserIdRequest::class, $request);
    }
}