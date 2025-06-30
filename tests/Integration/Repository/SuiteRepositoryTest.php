<?php

namespace WechatWorkProviderBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Repository\SuiteRepository;

class SuiteRepositoryTest extends TestCase
{
    public function testRepositoryExists(): void
    {
        $this->assertTrue(class_exists(SuiteRepository::class));
    }
}