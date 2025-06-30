<?php

namespace WechatWorkProviderBundle\Tests\Integration\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\EventSubscriber\SuiteListener;
use WechatWorkProviderBundle\Repository\SuiteRepository;

class SuiteListenerTest extends TestCase
{
    public function testListenerCanBeInstantiated(): void
    {
        $suiteRepository = $this->createMock(SuiteRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        $listener = new SuiteListener($suiteRepository, $entityManager);
        $this->assertInstanceOf(SuiteListener::class, $listener);
    }
}