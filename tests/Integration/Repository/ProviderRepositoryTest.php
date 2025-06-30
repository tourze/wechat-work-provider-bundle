<?php

namespace WechatWorkProviderBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Repository\ProviderRepository;

class ProviderRepositoryTest extends TestCase
{
    public function testRepositoryExists(): void
    {
        $this->assertTrue(class_exists(ProviderRepository::class));
    }
}