<?php

namespace WechatWorkProviderBundle\Tests\Integration\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatWorkProviderBundle\Command\RefreshAuthCorpAccessTokenCommand;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;
use WechatWorkProviderBundle\Service\ProviderService;

class RefreshAuthCorpAccessTokenCommandTest extends TestCase
{
    public function testCommandCanBeInstantiated(): void
    {
        $corpRepository = $this->createMock(AuthCorpRepository::class);
        $providerService = $this->createMock(ProviderService::class);
        $logger = $this->createMock(LoggerInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        $command = new RefreshAuthCorpAccessTokenCommand($corpRepository, $providerService, $logger, $entityManager);
        $this->assertInstanceOf(RefreshAuthCorpAccessTokenCommand::class, $command);
    }
}