<?php

namespace WechatWorkProviderBundle\Tests\LegacyApi;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\LegacyApi\ErrorCode;

/**
 * @internal
 */
#[CoversClass(ErrorCode::class)]
final class ErrorCodeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testClassExists(): void
    {
        $instance = new ErrorCode();
        $this->assertInstanceOf(ErrorCode::class, $instance);
    }
}
