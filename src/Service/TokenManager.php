<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Entity\Suite;
use WechatWorkProviderBundle\Exception\AccessTokenException;
use WechatWorkProviderBundle\Request\GetCorpTokenRequest;
use WechatWorkProviderBundle\Request\GetProviderTokenRequest;
use WechatWorkProviderBundle\Request\GetSuiteTokenRequest;

#[WithMonologChannel(channel: 'wechat_work_provider')]
class TokenManager
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestClientInterface $requestClient,
    ) {
    }

    public function ensureSuiteAccessToken(Suite $suite): string
    {
        $this->initializeTokenExpireTimeIfNeeded($suite);
        $this->clearExpiredSuiteTokenIfNeeded($suite);

        if ($this->shouldRefreshSuiteToken($suite)) {
            $this->refreshSuiteAccessToken($suite);
        }

        $token = $suite->getSuiteAccessToken();
        if (null === $token || '' === $token) {
            throw new \RuntimeException('Failed to get valid suite access token');
        }

        return $token;
    }

    public function ensureAuthCorpAccessToken(AuthCorp $authCorp): string
    {
        $this->initializeTokenExpireTimeIfNeeded($authCorp);
        $this->clearExpiredAuthCorpTokenIfNeeded($authCorp);

        if ($this->shouldRefreshAuthCorpToken($authCorp)) {
            $this->refreshAuthCorpAccessToken($authCorp);
        }

        $token = $authCorp->getAccessToken();
        if (null === $token || '' === $token) {
            throw new \RuntimeException('Failed to get valid auth corp access token');
        }

        return $token;
    }

    public function ensureProviderAccessToken(Provider $provider): string
    {
        $this->initializeTokenExpireTimeIfNeeded($provider);
        $this->clearExpiredProviderTokenIfNeeded($provider);

        if ($this->shouldRefreshProviderToken($provider)) {
            $this->refreshProviderAccessToken($provider);
        }

        $token = $provider->getProviderAccessToken();
        if (null === $token || '' === $token) {
            throw new \RuntimeException('Failed to get valid provider access token');
        }

        return $token;
    }

    private function initializeTokenExpireTimeIfNeeded(AuthCorp|Provider|Suite $entity): void
    {
        if (null === $entity->getTokenExpireTime()) {
            $entity->setTokenExpireTime(new \DateTimeImmutable('last day of december this year 23:59:59'));
        }
    }

    private function clearExpiredSuiteTokenIfNeeded(Suite $suite): void
    {
        $now = new \DateTimeImmutable();
        $tokenExpireTime = $suite->getTokenExpireTime();

        if (null !== $tokenExpireTime && $now > $tokenExpireTime) {
            $suite->setSuiteAccessToken('');
        }
    }

    private function clearExpiredAuthCorpTokenIfNeeded(AuthCorp $authCorp): void
    {
        $now = new \DateTimeImmutable();
        $tokenExpireTime = $authCorp->getTokenExpireTime();

        if (null !== $tokenExpireTime && $now > $tokenExpireTime) {
            $authCorp->setAccessToken('');
        }
    }

    private function clearExpiredProviderTokenIfNeeded(Provider $provider): void
    {
        $now = new \DateTimeImmutable();
        $token = $provider->getProviderAccessToken();
        $tokenExpireTime = $provider->getTokenExpireTime();

        if (null !== $token && '' !== $token && null !== $tokenExpireTime && $now >= $tokenExpireTime) {
            $provider->setProviderAccessToken('');
        }
    }

    private function shouldRefreshSuiteToken(Suite $suite): bool
    {
        return null === $suite->getSuiteAccessToken() || '' === $suite->getSuiteAccessToken();
    }

    private function shouldRefreshAuthCorpToken(AuthCorp $authCorp): bool
    {
        return null === $authCorp->getAccessToken() || '' === $authCorp->getAccessToken();
    }

    private function shouldRefreshProviderToken(Provider $provider): bool
    {
        return null === $provider->getProviderAccessToken() || '' === $provider->getProviderAccessToken();
    }

    private function refreshSuiteAccessToken(Suite $suite): void
    {
        $suiteId = $suite->getSuiteId();
        $suiteSecret = $suite->getSuiteSecret();
        $suiteTicket = $suite->getSuiteTicket();

        if (null === $suiteId || null === $suiteSecret || null === $suiteTicket) {
            $this->logger->error('Suite 信息不完整，无法刷新令牌', [
                'suite' => $suite,
                'suiteId' => $suiteId,
                'suiteSecret' => $suiteSecret,
                'suiteTicket' => $suiteTicket,
            ]);
            throw new AccessTokenException('Suite 信息不完整');
        }

        $tokenRequest = new GetSuiteTokenRequest();
        $tokenRequest->setSuiteId($suiteId);
        $tokenRequest->setSuiteSecret($suiteSecret);
        $tokenRequest->setSuiteTicket($suiteTicket);

        $tokenResponse = $this->requestClient->request($tokenRequest);

        if (!is_array($tokenResponse)) {
            throw new AccessTokenException('Suite令牌响应格式无效');
        }

        $suiteAccessToken = $tokenResponse['suite_access_token'] ?? null;
        if (!is_string($suiteAccessToken)) {
            throw new AccessTokenException('Suite访问令牌格式无效');
        }

        $expiresIn = $tokenResponse['expires_in'] ?? null;
        if (!is_int($expiresIn) && !is_float($expiresIn)) {
            throw new AccessTokenException('Suite令牌过期时间格式无效');
        }

        $suite->setSuiteAccessToken($suiteAccessToken);
        $suite->setTokenExpireTime((new \DateTimeImmutable())->modify("+{$expiresIn} seconds"));

        $this->entityManager->persist($suite);
        $this->entityManager->flush();
    }

    private function refreshAuthCorpAccessToken(AuthCorp $authCorp): void
    {
        $corpId = $authCorp->getCorpId();
        $permanentCode = $authCorp->getPermanentCode();

        if (null === $corpId || null === $permanentCode) {
            $this->logger->error('AuthCorp 信息不完整，无法刷新令牌', [
                'authCorp' => $authCorp,
                'corpId' => $corpId,
                'permanentCode' => $permanentCode,
            ]);
            throw new AccessTokenException('AuthCorp 信息不完整');
        }

        $tokenRequest = new GetCorpTokenRequest();
        $tokenRequest->setAuthCorpId($corpId);
        $tokenRequest->setPermanentCode($permanentCode);

        $tokenResponse = $this->requestClient->request($tokenRequest);

        if (!is_array($tokenResponse)) {
            throw new AccessTokenException('AuthCorp令牌响应格式无效');
        }

        $accessToken = $tokenResponse['access_token'] ?? null;
        if (!is_string($accessToken)) {
            $this->logger->error('access_token结果异常', [
                'authCorp' => $authCorp,
                'tokenResponse' => $tokenResponse,
            ]);
            throw new AccessTokenException('无法获取应用AccessToken');
        }

        $expiresIn = $tokenResponse['expires_in'] ?? null;
        if (!is_int($expiresIn) && !is_float($expiresIn)) {
            throw new AccessTokenException('AuthCorp令牌过期时间格式无效');
        }

        $authCorp->setAccessToken($accessToken);
        $authCorp->setTokenExpireTime((new \DateTimeImmutable())->modify("+{$expiresIn} seconds"));

        $this->entityManager->persist($authCorp);
        $this->entityManager->flush();
    }

    private function refreshProviderAccessToken(Provider $provider): void
    {
        $corpId = $provider->getCorpId();
        $providerSecret = $provider->getProviderSecret();

        if (null === $corpId || null === $providerSecret) {
            $this->logger->error('Provider 信息不完整，无法刷新令牌', [
                'provider' => $provider,
                'corpId' => $corpId,
                'providerSecret' => $providerSecret,
            ]);
            throw new AccessTokenException('Provider 信息不完整');
        }

        $tokenRequest = new GetProviderTokenRequest();
        $tokenRequest->setCorpId($corpId);
        $tokenRequest->setProviderSecret($providerSecret);

        $tokenResponse = $this->requestClient->request($tokenRequest);

        if (!is_array($tokenResponse)) {
            throw new AccessTokenException('Provider令牌响应格式无效');
        }

        $providerAccessToken = $tokenResponse['provider_access_token'] ?? null;
        if (!is_string($providerAccessToken)) {
            throw new AccessTokenException('Provider访问令牌格式无效');
        }

        $expiresIn = $tokenResponse['expires_in'] ?? null;
        if (!is_int($expiresIn) && !is_float($expiresIn)) {
            throw new AccessTokenException('Provider令牌过期时间格式无效');
        }

        $provider->setProviderAccessToken($providerAccessToken);
        $provider->setTokenExpireTime((new \DateTimeImmutable())->modify("+{$expiresIn} seconds"));

        $this->entityManager->persist($provider);
        $this->entityManager->flush();
    }
}
