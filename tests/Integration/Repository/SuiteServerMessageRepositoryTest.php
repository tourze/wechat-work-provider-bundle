<?php

namespace WechatWorkProviderBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Repository\SuiteServerMessageRepository;

class SuiteServerMessageRepositoryTest extends TestCase
{
    public function testRepositoryExists(): void
    {
        $this->assertTrue(class_exists(SuiteServerMessageRepository::class));
    }
}