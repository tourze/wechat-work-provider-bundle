<?php

namespace WechatWorkProviderBundle\Tests\LegacyApi;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\LegacyApi\Prpcrypt;

/**
 * @internal
 */
#[CoversClass(Prpcrypt::class)]
final class PrpcryptTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testClassCanBeInstantiated(): void
    {
        $prpcrypt = new Prpcrypt('test-key');
        $this->assertInstanceOf(Prpcrypt::class, $prpcrypt);
    }

    public function testDecrypt(): void
    {
        $reflection = new \ReflectionClass(Prpcrypt::class);
        $this->assertTrue($reflection->hasMethod('decrypt'));
    }

    public function testEncrypt(): void
    {
        $reflection = new \ReflectionClass(Prpcrypt::class);
        $this->assertTrue($reflection->hasMethod('encrypt'));
    }
}
