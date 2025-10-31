<?php

namespace WechatWorkProviderBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Entity\Suite;
use WechatWorkProviderBundle\Repository\SuiteRepository;

/**
 * @internal
 */
#[CoversClass(SuiteRepository::class)]
#[RunTestsInSeparateProcesses]
final class SuiteRepositoryTest extends AbstractRepositoryTestCase
{
    private SuiteRepository $repository;

    protected function onSetUp(): void
    {
    }

    private function initializeRepository(): void
    {
        $this->repository = self::getService(SuiteRepository::class);
    }

    protected function createNewEntity(): object
    {
        return $this->createSuite();
    }

    protected function getRepository(): SuiteRepository
    {
        if (!isset($this->repository)) {
            $this->initializeRepository();
        }

        return $this->repository;
    }

    public function testSave(): void
    {
        $this->initializeRepository();
        $suite = $this->createSuite();

        $this->repository->save($suite);
        $this->assertNotNull($suite->getId());

        $found = $this->repository->find($suite->getId());
        $this->assertSame($suite, $found);
    }

    public function testRemove(): void
    {
        $this->initializeRepository();
        $suite = $this->createSuite();
        $this->repository->save($suite);
        $id = $suite->getId();

        $this->repository->remove($suite);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFindByWithProviderAssociation(): void
    {
        $this->initializeRepository();
        $provider = $this->createProvider();
        $this->persistAndFlush($provider);

        $suite1 = $this->createSuite();
        $suite1->setProvider($provider);
        $suite2 = $this->createSuite('test_suite_456', 'secret_456');
        $suite2->setProvider($provider);

        $this->repository->save($suite1, false);
        $this->repository->save($suite2, false);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['provider' => $provider]);
        $this->assertGreaterThanOrEqual(2, count($results));

        foreach ($results as $suite) {
            $suiteProvider = $suite->getProvider();
            $this->assertNotNull($suiteProvider, '应用模板应该有关联的服务商');
            $this->assertEquals($provider->getId(), $suiteProvider->getId());
        }
    }

    public function testFindByWithNullSuiteTicket(): void
    {
        $this->initializeRepository();
        $suite = $this->createSuite();
        $suite->setSuiteTicket(null);
        $this->repository->save($suite);

        $results = $this->repository->findBy(['suiteTicket' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $s) {
            if ('test_suite_123' === $s->getSuiteId()) {
                $this->assertNull($s->getSuiteTicket());
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

        $suite1 = $this->createSuite();
        $suite1->setTokenExpireTime($expireTime);
        $suite2 = $this->createSuite('test_suite_456', 'secret_456');
        $suite2->setTokenExpireTime($expireTime);

        $this->repository->save($suite1, false);
        $this->repository->save($suite2, false);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['tokenExpireTime' => $expireTime]);
        $this->assertGreaterThanOrEqual(2, count($results));

        foreach ($results as $suite) {
            $this->assertEquals($expireTime, $suite->getTokenExpireTime());
        }
    }

    private function createSuite(string $suiteId = 'test_suite_123', string $suiteSecret = 'test_secret_123'): Suite
    {
        $provider = $this->createProvider();
        $this->persistAndFlush($provider);

        $suite = new Suite();
        $suite->setSuiteId($suiteId);
        $suite->setSuiteSecret($suiteSecret);
        $suite->setSuiteTicket('ticket_' . substr($suiteId, -3));
        $suite->setSuiteAccessToken('access_token_' . substr($suiteId, -3));
        $suite->setTokenExpireTime(new \DateTimeImmutable('+7200 seconds'));
        $suite->setTicketExpireTime(new \DateTimeImmutable('+3600 seconds'));
        $suite->setToken('token_' . substr($suiteId, -3));
        $suite->setEncodingAesKey('aes_key_' . substr($suiteId, -3));
        $suite->setProvider($provider);

        return $suite;
    }

    private function createProvider(): Provider
    {
        $provider = new Provider();
        $provider->setCorpId('test_provider_main');
        $provider->setProviderSecret('test_provider_secret');
        $provider->setProviderAccessToken('test_access_token');
        $provider->setTokenExpireTime(new \DateTimeImmutable('+7200 seconds'));
        $provider->setTicketExpireTime(new \DateTimeImmutable('+3600 seconds'));
        $provider->setToken('test_token');
        $provider->setEncodingAesKey('test_aes_key');

        return $provider;
    }
}
