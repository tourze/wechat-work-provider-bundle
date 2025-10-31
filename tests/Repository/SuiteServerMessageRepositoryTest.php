<?php

namespace WechatWorkProviderBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Entity\Suite;
use WechatWorkProviderBundle\Entity\SuiteServerMessage;
use WechatWorkProviderBundle\Repository\SuiteServerMessageRepository;

/**
 * @internal
 */
#[CoversClass(SuiteServerMessageRepository::class)]
#[RunTestsInSeparateProcesses]
final class SuiteServerMessageRepositoryTest extends AbstractRepositoryTestCase
{
    private SuiteServerMessageRepository $repository;

    protected function onSetUp(): void
    {
        // Repository 测试的自定义初始化逻辑
    }

    private function initializeRepository(): void
    {
        $this->repository = self::getService(SuiteServerMessageRepository::class);
    }

    protected function createNewEntity(): object
    {
        return $this->createSuiteServerMessage();
    }

    protected function getRepository(): SuiteServerMessageRepository
    {
        if (!isset($this->repository)) {
            $this->initializeRepository();
        }

        return $this->repository;
    }

    public function testSave(): void
    {
        $this->initializeRepository();
        $message = $this->createSuiteServerMessage();

        $this->repository->save($message);
        $this->assertGreaterThan(0, $message->getId());

        $found = $this->repository->find($message->getId());
        $this->assertSame($message, $found);
    }

    public function testRemove(): void
    {
        $this->initializeRepository();
        $message = $this->createSuiteServerMessage();
        $this->repository->save($message);
        $id = $message->getId();

        $this->repository->remove($message);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFindByWithSuiteAssociation(): void
    {
        $this->initializeRepository();
        $suite = $this->createSuite();
        $this->persistAndFlush($suite);

        $message1 = $this->createSuiteServerMessage();
        $message1->setSuite($suite);
        $message2 = $this->createSuiteServerMessage(['different' => 'context'], 'different raw data');
        $message2->setSuite($suite);

        $this->repository->save($message1, false);
        $this->repository->save($message2, false);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['suite' => $suite]);
        $this->assertGreaterThanOrEqual(2, count($results));

        foreach ($results as $message) {
            $messageSuite = $message->getSuite();
            $this->assertNotNull($messageSuite);
            $this->assertEquals($suite->getId(), $messageSuite->getId());
        }
    }

    public function testFindByWithNullSuite(): void
    {
        $this->initializeRepository();
        $message = $this->createSuiteServerMessage();
        $message->setSuite(null);
        $this->repository->save($message);

        $results = $this->repository->findBy(['suite' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $msg) {
            if ('test suite raw data' === $msg->getRawData()) {
                $this->assertNull($msg->getSuite());
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testFindByNullContext(): void
    {
        $this->initializeRepository();
        $message = $this->createSuiteServerMessage();
        $message->setContext(null);
        $this->repository->save($message);

        $results = $this->repository->findBy(['context' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $msg) {
            if ('test suite raw data' === $msg->getRawData()) {
                $this->assertNull($msg->getContext());
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testFindByNullRawData(): void
    {
        $this->initializeRepository();
        $message = $this->createSuiteServerMessage();
        $message->setRawData(null);
        $this->repository->save($message);

        $results = $this->repository->findBy(['rawData' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $msg) {
            if ($msg->getContext() === ['test' => 'suite_context']) {
                $this->assertNull($msg->getRawData());
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * @param array<string, string> $context
     */
    private function createSuiteServerMessage(array $context = ['test' => 'suite_context'], string $rawData = 'test suite raw data'): SuiteServerMessage
    {
        $message = new SuiteServerMessage();
        $message->setContext($context);
        $message->setRawData($rawData);

        return $message;
    }

    private function createSuite(): Suite
    {
        $provider = $this->createProvider();
        $this->persistAndFlush($provider);

        $suite = new Suite();
        $suite->setSuiteId('test_suite_main');
        $suite->setSuiteSecret('test_suite_secret');
        $suite->setSuiteTicket('test_ticket');
        $suite->setSuiteAccessToken('test_access_token');
        $suite->setTokenExpireTime(new \DateTimeImmutable('+7200 seconds'));
        $suite->setTicketExpireTime(new \DateTimeImmutable('+3600 seconds'));
        $suite->setToken('test_token');
        $suite->setEncodingAesKey('test_aes_key');
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
