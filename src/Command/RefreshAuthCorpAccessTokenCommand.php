<?php

namespace WechatWorkProviderBundle\Command;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;
use WechatWorkProviderBundle\Request\GetCorpTokenRequest;
use WechatWorkProviderBundle\Service\ProviderService;

#[AsCronTask('* * * * *')]
#[AsCommand(name: 'wechat-work-provider:refresh-auth-corp-access-token', description: '刷新代开发授权应用access_token')]
class RefreshAuthCorpAccessTokenCommand extends Command
{
    public function __construct(
        private readonly AuthCorpRepository $corpRepository,
        private readonly ProviderService $providerService,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->corpRepository->findAll() as $authCorp) {
            // 定时任务刷新的时候，提前几分钟过期
            $now = Carbon::now()->subMinutes(5);

            if (!$authCorp->getTokenExpireTime()) {
                $authCorp->setTokenExpireTime(Carbon::now()->lastOfYear());
            }
            if ($authCorp->getAccessToken() && $now->greaterThan($authCorp->getTokenExpireTime())) {
                $authCorp->setAccessToken('');
            }

            if (!$authCorp->getAccessToken()) {
                $tokenRequest = new GetCorpTokenRequest();
                $tokenRequest->setAuthCorpId($authCorp->getCorpId());
                $tokenRequest->setPermanentCode($authCorp->getPermanentCode());
                $tokenResponse = $this->providerService->request($tokenRequest);
                if (!isset($tokenResponse['access_token'])) {
                    $this->logger->error('定时任务获取AccessToken失败', [
                        'authCorp' => $authCorp,
                        'tokenResponse' => $tokenResponse,
                    ]);
                    continue;
                }

                $authCorp->setAccessToken($tokenResponse['access_token']);
                $authCorp->setTokenExpireTime(Carbon::now()->addSeconds($tokenResponse['expires_in']));
                $this->entityManager->persist($authCorp);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
