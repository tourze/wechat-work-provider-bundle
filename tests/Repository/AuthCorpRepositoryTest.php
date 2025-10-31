<?php

namespace WechatWorkProviderBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Entity\Suite;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;

/**
 * @internal
 */
#[CoversClass(AuthCorpRepository::class)]
#[RunTestsInSeparateProcesses]
final class AuthCorpRepositoryTest extends AbstractRepositoryTestCase
{
    private AuthCorpRepository $repository;

    protected function onSetUp(): void
    {
    }

    private function initializeRepository(): void
    {
        $this->repository = self::getService(AuthCorpRepository::class);
    }

    protected function createNewEntity(): object
    {
        return $this->createAuthCorp();
    }

    protected function getRepository(): AuthCorpRepository
    {
        if (!isset($this->repository)) {
            $this->initializeRepository();
        }

        return $this->repository;
    }

    public function testSave(): void
    {
        $this->initializeRepository();
        $authCorp = $this->createAuthCorp();

        $this->repository->save($authCorp);
        $this->assertNotNull($authCorp->getId());

        $found = $this->repository->find($authCorp->getId());
        $this->assertSame($authCorp, $found);
    }

    public function testRemove(): void
    {
        $this->initializeRepository();
        $authCorp = $this->createAuthCorp();
        $this->repository->save($authCorp);
        $id = $authCorp->getId();

        $this->repository->remove($authCorp);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFindByWithSuiteAssociation(): void
    {
        $this->initializeRepository();
        $suite = $this->createSuite();
        $this->persistAndFlush($suite);

        $authCorp1 = $this->createAuthCorp();
        $authCorp1->setSuite($suite);
        $authCorp2 = $this->createAuthCorp('corp_test_456', '另一个测试企业');
        $authCorp2->setSuite($suite);

        $this->repository->save($authCorp1, false);
        $this->repository->save($authCorp2, false);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['suite' => $suite]);
        $this->assertGreaterThanOrEqual(2, count($results));

        foreach ($results as $authCorp) {
            $authCorpSuite = $authCorp->getSuite();
            $this->assertNotNull($authCorpSuite, '授权企业应该有关联的应用模板');
            $this->assertEquals($suite->getId(), $authCorpSuite->getId());
        }
    }

    public function testFindByWithNullSuite(): void
    {
        $this->initializeRepository();
        $authCorp = $this->createAuthCorp();
        $authCorp->setSuite(null);
        $this->repository->save($authCorp);

        $results = $this->repository->findBy(['suite' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $corp) {
            if ('corp_test_123' === $corp->getCorpId()) {
                $this->assertNull($corp->getSuite());
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testGetAgentByAuthCorp(): void
    {
        $this->initializeRepository();
        $authCorp = $this->createAuthCorp();
        $this->repository->save($authCorp);

        $agent = $this->repository->getAgentByAuthCorp($authCorp);
        $this->assertNull($agent);
    }

    private function createAuthCorp(string $corpId = 'corp_test_123', string $corpName = '测试企业'): AuthCorp
    {
        $authCorp = new AuthCorp();
        $authCorp->setCorpId($corpId);
        $authCorp->setCorpName($corpName);
        $authCorp->setCorpType('verified');
        $authCorp->setCorpUserMax(100);
        $authCorp->setCorpFullName($corpName . '有限公司');
        $authCorp->setSubjectType('enterprise');
        $authCorp->setCorpScale('50-200人');
        $authCorp->setCorpIndustry('IT服务');
        $authCorp->setCorpSubIndustry('软件开发');
        $authCorp->setPermanentCode('permanent_code_' . substr($corpId, -3));

        return $authCorp;
    }

    private function createSuite(): Suite
    {
        // 先创建Provider
        $provider = new Provider();
        $provider->setCorpId('test_provider_corp_main');
        $provider->setProviderSecret('test_provider_secret');
        $provider->setToken('test_token');
        $provider->setEncodingAesKey('test_aes_key');
        self::getEntityManager()->persist($provider);

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
}
