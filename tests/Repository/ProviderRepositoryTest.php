<?php

namespace WechatWorkProviderBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Repository\ProviderRepository;

/**
 * @internal
 */
#[CoversClass(ProviderRepository::class)]
#[RunTestsInSeparateProcesses]
final class ProviderRepositoryTest extends AbstractRepositoryTestCase
{
    private ProviderRepository $repository;

    protected function onSetUp(): void
    {
        // Repository 测试的自定义初始化逻辑
    }

    private function initializeRepository(): void
    {
        $this->repository = self::getService(ProviderRepository::class);
    }

    protected function createNewEntity(): object
    {
        return $this->createProvider();
    }

    protected function getRepository(): ProviderRepository
    {
        if (!isset($this->repository)) {
            $this->initializeRepository();
        }

        return $this->repository;
    }

    public function testSave(): void
    {
        $this->initializeRepository();
        $provider = $this->createProvider();

        $this->repository->save($provider);
        $this->assertNotNull($provider->getId());

        $found = $this->repository->find($provider->getId());
        $this->assertSame($provider, $found);
    }

    public function testRemove(): void
    {
        $this->initializeRepository();
        $provider = $this->createProvider();
        $this->repository->save($provider);
        $id = $provider->getId();

        $this->repository->remove($provider);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFindByWithNullToken(): void
    {
        $this->initializeRepository();
        $provider = $this->createProvider();
        $provider->setToken(null);
        $this->repository->save($provider);

        $results = $this->repository->findBy(['token' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $p) {
            if ('test_corp_123' === $p->getCorpId()) {
                $this->assertNull($p->getToken());
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testFindByWithNullProviderAccessToken(): void
    {
        $this->initializeRepository();
        $provider = $this->createProvider();
        $provider->setProviderAccessToken(null);
        $this->repository->save($provider);

        $results = $this->repository->findBy(['providerAccessToken' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $p) {
            if ('test_corp_123' === $p->getCorpId()) {
                $this->assertNull($p->getProviderAccessToken());
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testFindByTokenExpireTime(): void
    {
        $this->initializeRepository();
        $expireTime = new \DateTimeImmutable('2024-12-31 23:59:59');

        $provider1 = $this->createProvider();
        $provider1->setTokenExpireTime($expireTime);
        $provider2 = $this->createProvider('test_corp_456', 'secret_456');
        $provider2->setTokenExpireTime($expireTime);

        $this->repository->save($provider1, false);
        $this->repository->save($provider2, false);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['tokenExpireTime' => $expireTime]);
        $this->assertGreaterThanOrEqual(2, count($results));

        foreach ($results as $provider) {
            $this->assertEquals($expireTime, $provider->getTokenExpireTime());
        }
    }

    private function createProvider(string $corpId = 'test_corp_123', string $providerSecret = 'test_secret_123'): Provider
    {
        $provider = new Provider();
        $provider->setCorpId($corpId);
        $provider->setProviderSecret($providerSecret);
        $provider->setProviderAccessToken('access_token_' . substr($corpId, -3));
        $provider->setTokenExpireTime(new \DateTimeImmutable('+7200 seconds'));
        $provider->setTicketExpireTime(new \DateTimeImmutable('+3600 seconds'));
        $provider->setToken('token_' . substr($corpId, -3));
        $provider->setEncodingAesKey('aes_key_' . substr($corpId, -3));

        return $provider;
    }
}
