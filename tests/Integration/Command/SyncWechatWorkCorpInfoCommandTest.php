<?php

namespace WechatWorkProviderBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Command\SyncWechatWorkCorpInfoCommand;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;
use WechatWorkProviderBundle\Service\ProviderService;

class SyncWechatWorkCorpInfoCommandTest extends TestCase
{
    public function testCommandCanBeInstantiated(): void
    {
        $authCorpRepository = $this->createMock(AuthCorpRepository::class);
        $providerService = $this->createMock(ProviderService::class);
        
        $command = new SyncWechatWorkCorpInfoCommand($authCorpRepository, $providerService);
        $this->assertInstanceOf(SyncWechatWorkCorpInfoCommand::class, $command);
    }
}