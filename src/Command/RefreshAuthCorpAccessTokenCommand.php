<?php

namespace WechatWorkProviderBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;
use WechatWorkProviderBundle\Request\GetCorpTokenRequest;
use WechatWorkProviderBundle\Service\ProviderService;

#[AsCronTask(expression: '* * * * *')]
#[AsCommand(name: self::NAME, description: '刷新代开发授权应用access_token')]
#[WithMonologChannel(channel: 'wechat_work_provider')]
class RefreshAuthCorpAccessTokenCommand extends Command
{
    private const NAME = 'wechat-work-provider:refresh-auth-corp-access-token';

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
            $this->refreshCorpAccessToken($authCorp);
        }

        return Command::SUCCESS;
    }

    private function refreshCorpAccessToken(AuthCorp $authCorp): void
    {
        $this->initializeTokenExpireTime($authCorp);
        $this->clearExpiredToken($authCorp);

        if ($this->shouldRefreshToken($authCorp)) {
            $this->performTokenRefresh($authCorp);
        }
    }

    private function initializeTokenExpireTime(AuthCorp $authCorp): void
    {
        if (null === $authCorp->getTokenExpireTime()) {
            $authCorp->setTokenExpireTime(CarbonImmutable::now()->lastOfYear());
        }
    }

    private function clearExpiredToken(AuthCorp $authCorp): void
    {
        $now = CarbonImmutable::now()->subMinutes(5);
        $hasValidToken = null !== $authCorp->getAccessToken() && '' !== $authCorp->getAccessToken();

        $tokenExpireTime = $authCorp->getTokenExpireTime();
        if ($hasValidToken && null !== $tokenExpireTime && $now->greaterThan($tokenExpireTime)) {
            $authCorp->setAccessToken('');
        }
    }

    private function shouldRefreshToken(AuthCorp $authCorp): bool
    {
        return null === $authCorp->getAccessToken() || '' === $authCorp->getAccessToken();
    }

    private function performTokenRefresh(AuthCorp $authCorp): void
    {
        $corpId = $authCorp->getCorpId();
        $permanentCode = $authCorp->getPermanentCode();

        if (null === $corpId || null === $permanentCode) {
            $this->logger->error('AuthCorp 信息不完整，无法刷新令牌', [
                'authCorp' => $authCorp,
                'corpId' => $corpId,
                'permanentCode' => $permanentCode,
            ]);

            return;
        }

        $tokenRequest = new GetCorpTokenRequest();
        $tokenRequest->setAuthCorpId($corpId);
        $tokenRequest->setPermanentCode($permanentCode);

        $tokenResponse = $this->providerService->request($tokenRequest);

        // 确保响应是数组类型
        if (!is_array($tokenResponse)) {
            $this->logger->error('定时任务获取AccessToken失败：响应格式错误', [
                'authCorp' => $authCorp,
                'tokenResponse' => $tokenResponse,
            ]);

            return;
        }

        if (!isset($tokenResponse['access_token'])) {
            $this->logger->error('定时任务获取AccessToken失败', [
                'authCorp' => $authCorp,
                'tokenResponse' => $tokenResponse,
            ]);

            return;
        }

        // 验证access_token类型
        $accessToken = $tokenResponse['access_token'];
        if (!is_string($accessToken)) {
            $this->logger->error('定时任务获取AccessToken失败：access_token类型错误', [
                'authCorp' => $authCorp,
                'accessToken' => $accessToken,
            ]);

            return;
        }

        // 验证expires_in类型
        $expiresIn = $tokenResponse['expires_in'] ?? null;
        if (!is_int($expiresIn) && !is_float($expiresIn)) {
            $this->logger->error('定时任务获取AccessToken失败：expires_in类型错误', [
                'authCorp' => $authCorp,
                'expiresIn' => $expiresIn,
            ]);

            return;
        }

        $authCorp->setAccessToken($accessToken);
        $authCorp->setTokenExpireTime(CarbonImmutable::now()->addSeconds($expiresIn));
        $this->entityManager->persist($authCorp);
        $this->entityManager->flush();
    }
}
