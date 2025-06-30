<?php

namespace WechatWorkProviderBundle\Tests\Unit\LegacyApi;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\LegacyApi\ErrorCode;

class ErrorCodeTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(ErrorCode::class));
    }
}