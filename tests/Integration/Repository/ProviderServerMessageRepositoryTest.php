<?php

namespace WechatWorkProviderBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Repository\ProviderServerMessageRepository;

class ProviderServerMessageRepositoryTest extends TestCase
{
    public function testRepositoryExists(): void
    {
        $this->assertTrue(class_exists(ProviderServerMessageRepository::class));
    }
}