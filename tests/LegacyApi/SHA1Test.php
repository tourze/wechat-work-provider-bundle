<?php

namespace WechatWorkProviderBundle\Tests\LegacyApi;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\LegacyApi\SHA1;

/**
 * @internal
 */
#[CoversClass(SHA1::class)]
final class SHA1Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testClassExists(): void
    {
        $instance = new SHA1();
        $this->assertInstanceOf(SHA1::class, $instance);
    }
}
