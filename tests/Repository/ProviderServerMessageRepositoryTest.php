<?php

namespace WechatWorkProviderBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Entity\ProviderServerMessage;
use WechatWorkProviderBundle\Repository\ProviderServerMessageRepository;

/**
 * @internal
 */
#[CoversClass(ProviderServerMessageRepository::class)]
#[RunTestsInSeparateProcesses]
final class ProviderServerMessageRepositoryTest extends AbstractRepositoryTestCase
{
    private ProviderServerMessageRepository $repository;

    protected function onSetUp(): void
    {
        // Repository 测试的自定义初始化逻辑
    }

    private function initializeRepository(): void
    {
        $this->repository = self::getService(ProviderServerMessageRepository::class);
    }

    protected function createNewEntity(): object
    {
        return $this->createProviderServerMessage();
    }

    protected function getRepository(): ProviderServerMessageRepository
    {
        if (!isset($this->repository)) {
            $this->initializeRepository();
        }

        return $this->repository;
    }

    public function testSave(): void
    {
        $this->initializeRepository();
        $message = $this->createProviderServerMessage();

        $this->repository->save($message);
        $this->assertGreaterThan(0, $message->getId());

        $found = $this->repository->find($message->getId());
        $this->assertSame($message, $found);
    }

    public function testRemove(): void
    {
        $this->initializeRepository();
        $message = $this->createProviderServerMessage();
        $this->repository->save($message);
        $id = $message->getId();

        $this->repository->remove($message);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFindByWithProviderAssociation(): void
    {
        $this->initializeRepository();
        $provider = $this->createProvider();
        $this->persistAndFlush($provider);

        $message1 = $this->createProviderServerMessage();
        $message1->setProvider($provider);
        $message2 = $this->createProviderServerMessage(['different' => 'context'], 'different raw data');
        $message2->setProvider($provider);

        $this->repository->save($message1, false);
        $this->repository->save($message2, false);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['provider' => $provider]);
        $this->assertGreaterThanOrEqual(2, count($results));

        foreach ($results as $message) {
            $messageProvider = $message->getProvider();
            $this->assertNotNull($messageProvider);
            $this->assertEquals($provider->getId(), $messageProvider->getId());
        }
    }

    public function testFindByWithNullProvider(): void
    {
        $this->initializeRepository();
        $message = $this->createProviderServerMessage();
        $message->setProvider(null);
        $this->repository->save($message);

        $results = $this->repository->findBy(['provider' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $msg) {
            if ('test raw data' === $msg->getRawData()) {
                $this->assertNull($msg->getProvider());
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testFindByNullContext(): void
    {
        $this->initializeRepository();
        $message = $this->createProviderServerMessage();
        $message->setContext(null);
        $this->repository->save($message);

        $results = $this->repository->findBy(['context' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $msg) {
            if ('test raw data' === $msg->getRawData()) {
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
        $message = $this->createProviderServerMessage();
        $message->setRawData(null);
        $this->repository->save($message);

        $results = $this->repository->findBy(['rawData' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $msg) {
            if ($msg->getContext() === ['test' => 'context']) {
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
    private function createProviderServerMessage(array $context = ['test' => 'context'], string $rawData = 'test raw data'): ProviderServerMessage
    {
        $message = new ProviderServerMessage();
        $message->setContext($context);
        $message->setRawData($rawData);

        return $message;
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
