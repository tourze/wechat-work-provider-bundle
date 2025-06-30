<?php

namespace WechatWorkProviderBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Entity\Suite;

class SuiteTest extends TestCase
{
    public function testEntityCanBeInstantiated(): void
    {
        $entity = new Suite();
        $this->assertInstanceOf(Suite::class, $entity);
    }
}