<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Entity\Suite;
use WechatWorkProviderBundle\Exception\AccessTokenException;
use WechatWorkProviderBundle\Service\TokenManager;

/**
 * @internal
 */
#[CoversClass(TokenManager::class)]
#[RunTestsInSeparateProcesses]
class TokenManagerTest extends AbstractIntegrationTestCase
{
    private TokenManager $tokenManager;

    protected function onSetUp(): void
    {
        $this->tokenManager = self::getService(TokenManager::class);
    }

    public function testEnsureSuiteAccessTokenWithValidToken(): void
    {
        $suite = new Suite();
        $suite->setSuiteAccessToken('valid_token');
        $suite->setTokenExpireTime(new \DateTimeImmutable('+1 hour'));

        $result = $this->tokenManager->ensureSuiteAccessToken($suite);

        self::assertSame('valid_token', $result);
    }

    public function testEnsureAuthCorpAccessTokenWithValidToken(): void
    {
        $authCorp = new AuthCorp();
        $authCorp->setAccessToken('valid_token');
        $authCorp->setTokenExpireTime(new \DateTimeImmutable('+1 hour'));

        $result = $this->tokenManager->ensureAuthCorpAccessToken($authCorp);

        self::assertSame('valid_token', $result);
    }

    public function testEnsureProviderAccessTokenWithValidToken(): void
    {
        $provider = new Provider();
        $provider->setProviderAccessToken('valid_token');
        $provider->setTokenExpireTime(new \DateTimeImmutable('+1 hour'));

        $result = $this->tokenManager->ensureProviderAccessToken($provider);

        self::assertSame('valid_token', $result);
    }

    public function testEnsureSuiteAccessTokenThrowsExceptionOnEmptyToken(): void
    {
        $suite = new Suite();
        $suite->setSuiteAccessToken('');
        $suite->setTokenExpireTime(new \DateTimeImmutable('+1 hour'));

        self::expectException(AccessTokenException::class);
        self::expectExceptionMessage('Suite 信息不完整');

        $this->tokenManager->ensureSuiteAccessToken($suite);
    }

    public function testEnsureAuthCorpAccessTokenThrowsExceptionOnEmptyToken(): void
    {
        $authCorp = new AuthCorp();
        $authCorp->setAccessToken('');
        $authCorp->setTokenExpireTime(new \DateTimeImmutable('+1 hour'));

        self::expectException(AccessTokenException::class);
        self::expectExceptionMessage('AuthCorp 信息不完整');

        $this->tokenManager->ensureAuthCorpAccessToken($authCorp);
    }

    public function testEnsureProviderAccessTokenThrowsExceptionOnEmptyToken(): void
    {
        $provider = new Provider();
        $provider->setProviderAccessToken('');
        $provider->setTokenExpireTime(new \DateTimeImmutable('+1 hour'));

        self::expectException(AccessTokenException::class);
        self::expectExceptionMessage('Provider 信息不完整');

        $this->tokenManager->ensureProviderAccessToken($provider);
    }
}
