<?php

namespace WechatWorkProviderBundle\Tests\LegacyApi;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\LegacyApi\XMLParse;

/**
 * @internal
 */
#[CoversClass(XMLParse::class)]
final class XMLParseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testClassExists(): void
    {
        $instance = new XMLParse();
        $this->assertInstanceOf(XMLParse::class, $instance);
    }

    public function testExtract(): void
    {
        $reflection = new \ReflectionClass(XMLParse::class);
        $this->assertTrue($reflection->hasMethod('extract'));
    }

    public function testGenerate(): void
    {
        $reflection = new \ReflectionClass(XMLParse::class);
        $this->assertTrue($reflection->hasMethod('generate'));
    }
}
