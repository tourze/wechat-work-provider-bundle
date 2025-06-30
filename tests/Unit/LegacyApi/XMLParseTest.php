<?php

namespace WechatWorkProviderBundle\Tests\Unit\LegacyApi;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\LegacyApi\XMLParse;

class XMLParseTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(XMLParse::class));
    }
}