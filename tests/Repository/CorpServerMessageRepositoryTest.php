<?php

namespace WechatWorkProviderBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Entity\CorpServerMessage;
use WechatWorkProviderBundle\Repository\CorpServerMessageRepository;

/**
 * @internal
 */
#[CoversClass(CorpServerMessageRepository::class)]
#[RunTestsInSeparateProcesses]
final class CorpServerMessageRepositoryTest extends AbstractRepositoryTestCase
{
    private CorpServerMessageRepository $repository;

    protected function onSetUp(): void
    {
    }

    private function initializeRepository(): void
    {
        $this->repository = self::getService(CorpServerMessageRepository::class);
    }

    protected function createNewEntity(): object
    {
        return $this->createCorpServerMessage();
    }

    protected function getRepository(): CorpServerMessageRepository
    {
        if (!isset($this->repository)) {
            $this->initializeRepository();
        }

        return $this->repository;
    }

    public function testSave(): void
    {
        $this->initializeRepository();
        $message = $this->createCorpServerMessage();

        $this->repository->save($message);
        $this->assertNotNull($message->getId());

        $found = $this->repository->find($message->getId());
        $this->assertSame($message, $found);
    }

    public function testRemove(): void
    {
        $this->initializeRepository();
        $message = $this->createCorpServerMessage();
        $this->repository->save($message);
        $id = $message->getId();

        $this->repository->remove($message);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFindByWithAuthCorpAssociation(): void
    {
        $this->initializeRepository();
        $authCorp = $this->createAuthCorp();
        $this->persistAndFlush($authCorp);

        $message1 = $this->createCorpServerMessage();
        $message1->setAuthCorp($authCorp);
        $message2 = $this->createCorpServerMessage('test_to_user_2', 'from_user_2');
        $message2->setAuthCorp($authCorp);

        $this->repository->save($message1, false);
        $this->repository->save($message2, false);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['authCorp' => $authCorp]);
        $this->assertGreaterThanOrEqual(2, count($results));

        foreach ($results as $message) {
            $messageAuthCorp = $message->getAuthCorp();
            $this->assertNotNull($messageAuthCorp, '消息应该有关联的授权企业');
            $this->assertEquals($authCorp->getId(), $messageAuthCorp->getId());
        }
    }

    public function testFindByWithNullAuthCorp(): void
    {
        $this->initializeRepository();
        $message = $this->createCorpServerMessage();
        $message->setAuthCorp(null);
        $this->repository->save($message);

        $results = $this->repository->findBy(['authCorp' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $msg) {
            if ('test_to_user' === $msg->getToUserName()) {
                $this->assertNull($msg->getAuthCorp());
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testFindByMsgType(): void
    {
        $this->initializeRepository();
        $message1 = $this->createCorpServerMessage();
        $message1->setMsgType('text');
        $message2 = $this->createCorpServerMessage('test_to_user_2', 'from_user_2');
        $message2->setMsgType('event');

        $this->repository->save($message1, false);
        $this->repository->save($message2, false);
        self::getEntityManager()->flush();

        $textResults = $this->repository->findBy(['msgType' => 'text']);
        $this->assertGreaterThanOrEqual(1, count($textResults));

        $eventResults = $this->repository->findBy(['msgType' => 'event']);
        $this->assertGreaterThanOrEqual(1, count($eventResults));
    }

    private function createCorpServerMessage(string $toUserName = 'test_to_user', string $fromUserName = 'test_from_user'): CorpServerMessage
    {
        $message = new CorpServerMessage();
        $message->setToUserName($toUserName);
        $message->setFromUserName($fromUserName);
        $message->setCreateTime(time());
        $message->setDecryptData(['test' => 'data']);
        $message->setRawData(['raw' => 'data']);
        $message->setMsgType('text');
        $message->setEvent('test_event');
        $message->setChangeType('add');

        return $message;
    }

    private function createAuthCorp(): AuthCorp
    {
        $authCorp = new AuthCorp();
        $authCorp->setCorpId('test_corp_auth');
        $authCorp->setCorpName('测试认证企业');
        $authCorp->setCorpType('verified');
        $authCorp->setCorpUserMax(100);
        $authCorp->setCorpFullName('测试认证企业有限公司');
        $authCorp->setSubjectType('enterprise');
        $authCorp->setCorpScale('50-200人');
        $authCorp->setCorpIndustry('IT服务');
        $authCorp->setCorpSubIndustry('软件开发');
        $authCorp->setPermanentCode('permanent_code_auth');

        return $authCorp;
    }
}
