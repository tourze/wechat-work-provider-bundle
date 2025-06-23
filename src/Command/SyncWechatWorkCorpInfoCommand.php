<?php

namespace WechatWorkProviderBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;
use WechatWorkProviderBundle\Service\ProviderService;

#[AsCronTask('* * * * *')]
#[AsCommand(name: self::NAME, description: '将授权的企业信息同步一份到WechatWorkBundle')]
class SyncWechatWorkCorpInfoCommand extends Command
{
    public const NAME = 'wechat-work-provider:sync-corp-info';

    public function __construct(
        private readonly AuthCorpRepository $authCorpRepository,
        private readonly ProviderService $providerService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->authCorpRepository->findAll() as $authCorp) {
            $this->providerService->syncAuthCorpToCorpAndAgent($authCorp);
        }

        return Command::SUCCESS;
    }
}
