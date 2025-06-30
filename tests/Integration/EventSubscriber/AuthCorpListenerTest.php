<?php

namespace WechatWorkProviderBundle\Tests\Integration\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\EventSubscriber\AuthCorpListener;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;
use WechatWorkProviderBundle\Repository\SuiteRepository;
use WechatWorkProviderBundle\Service\ProviderService;

class AuthCorpListenerTest extends TestCase
{
    public function testListenerCanBeInstantiated(): void
    {
        $suiteRepository = $this->createMock(SuiteRepository::class);
        $providerService = $this->createMock(ProviderService::class);
        $authCorpRepository = $this->createMock(AuthCorpRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        $listener = new AuthCorpListener($suiteRepository, $providerService, $authCorpRepository, $entityManager);
        $this->assertInstanceOf(AuthCorpListener::class, $listener);
    }
}