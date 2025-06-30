<?php

namespace WechatWorkProviderBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Repository\CorpServerMessageRepository;

class CorpServerMessageRepositoryTest extends TestCase
{
    public function testRepositoryExists(): void
    {
        $this->assertTrue(class_exists(CorpServerMessageRepository::class));
    }
}