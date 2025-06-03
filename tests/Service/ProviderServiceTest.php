<?php

namespace WechatWorkProviderBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;
use WechatWorkProviderBundle\Repository\SuiteRepository;
use WechatWorkProviderBundle\Service\ProviderService;

class ProviderServiceTest extends TestCase
{
    private ProviderService $providerService;
    /** @var SuiteRepository&MockObject */
    private MockObject $suiteRepository;
    /** @var AuthCorpRepository&MockObject */
    private MockObject $authCorpRepository;
    /** @var CorpRepository&MockObject */
    private MockObject $corpRepository;
    /** @var AgentRepository&MockObject */
    private MockObject $agentRepository;
    /** @var EntityManagerInterface&MockObject */
    private MockObject $entityManager;

    protected function setUp(): void
    {
        $this->suiteRepository = $this->createMock(SuiteRepository::class);
        $this->authCorpRepository = $this->createMock(AuthCorpRepository::class);
        $this->corpRepository = $this->createMock(CorpRepository::class);
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->providerService = new ProviderService(
            $this->suiteRepository,
            $this->authCorpRepository,
            $this->corpRepository,
            $this->agentRepository,
            $this->entityManager
        );
    }

    public function testGetBaseUrl(): void
    {
        $expectedUrl = 'https://qyapi.weixin.qq.com';
        $this->assertSame($expectedUrl, $this->providerService->getBaseUrl());
    }

    public function testSyncAuthCorpToCorpAndAgentWithExistingCorp(): void
    {
        // 准备数据
        $authCorp = new AuthCorp();
        $authCorp->setCorpId('test_corp_id');
        $authCorp->setCorpName('测试企业');
        $authCorp->setAccessToken('access_token_123');
        $authCorp->setTokenExpireTime(new \DateTime('2024-12-31'));
        $authCorp->setAuthInfo([
            'agent' => [
                [
                    'agentid' => 1000001,
                    'name' => '测试应用',
                    'square_logo_url' => 'https://example.com/logo.png'
                ]
            ]
        ]);

        $existingCorp = new Corp();
        $existingCorp->setCorpId('test_corp_id');

        // 模拟存储库行为
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'test_corp_id'])
            ->willReturn($existingCorp);

        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $existingCorp,
                'agentId' => 1000001
            ])
            ->willReturn(null);

        // 模拟实体管理器行为
        $this->entityManager->expects($this->exactly(2))
            ->method('persist');
        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        // 执行方法
        $result = $this->providerService->syncAuthCorpToCorpAndAgent($authCorp);

        // 验证结果
        $this->assertSame($existingCorp, $result);
        $this->assertTrue($existingCorp->isFromProvider());
        $this->assertSame('测试企业', $existingCorp->getName());
    }

    public function testSyncAuthCorpToCorpAndAgentWithNewCorp(): void
    {
        // 准备数据
        $authCorp = new AuthCorp();
        $authCorp->setCorpId('new_corp_id');
        $authCorp->setCorpName('新企业');
        $authCorp->setAccessToken('access_token_456');
        $authCorp->setTokenExpireTime(new \DateTime('2024-12-31'));
        $authCorp->setAuthInfo([
            'agent' => [
                [
                    'agentid' => 1000002,
                    'name' => '新应用',
                    'square_logo_url' => 'https://example.com/new_logo.png'
                ]
            ]
        ]);

        // 模拟存储库行为 - 没有找到现有企业
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'new_corp_id'])
            ->willReturn(null);

        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        // 模拟实体管理器行为
        $this->entityManager->expects($this->exactly(2))
            ->method('persist');
        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        // 执行方法
        $result = $this->providerService->syncAuthCorpToCorpAndAgent($authCorp);

        // 验证结果
        $this->assertInstanceOf(Corp::class, $result);
        $this->assertTrue($result->isFromProvider());
        $this->assertSame('new_corp_id', $result->getCorpId());
        $this->assertSame('新企业', $result->getName());
    }

    public function testSyncAuthCorpToCorpAndAgentWithExistingAgent(): void
    {
        // 准备数据
        $authCorp = new AuthCorp();
        $authCorp->setCorpId('test_corp_id');
        $authCorp->setCorpName('测试企业');
        $authCorp->setAccessToken('access_token_789');
        $authCorp->setTokenExpireTime(new \DateTime('2024-12-31'));
        $authCorp->setAuthInfo([
            'agent' => [
                [
                    'agentid' => 1000003,
                    'name' => '更新的应用',
                    'square_logo_url' => 'https://example.com/updated_logo.png'
                ]
            ]
        ]);

        $existingCorp = new Corp();
        $existingCorp->setCorpId('test_corp_id');

        $existingAgent = new Agent();
        $existingAgent->setCorp($existingCorp);
        $existingAgent->setAgentId(1000003);
        $existingAgent->setName('旧应用名称');

        // 模拟存储库行为
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'test_corp_id'])
            ->willReturn($existingCorp);

        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $existingCorp,
                'agentId' => 1000003
            ])
            ->willReturn($existingAgent);

        // 模拟实体管理器行为
        $this->entityManager->expects($this->exactly(2))
            ->method('persist');
        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        // 执行方法
        $result = $this->providerService->syncAuthCorpToCorpAndAgent($authCorp);

        // 验证应用信息被更新
        $this->assertSame('更新的应用', $existingAgent->getName());
        $this->assertSame('https://example.com/updated_logo.png', $existingAgent->getSquareLogoUrl());
        $this->assertSame('access_token_789', $existingAgent->getAccessToken());
    }

    public function testSyncAuthCorpToCorpAndAgentWithMultipleAgents(): void
    {
        // 准备数据
        $authCorp = new AuthCorp();
        $authCorp->setCorpId('multi_corp_id');
        $authCorp->setCorpName('多应用企业');
        $authCorp->setAccessToken('multi_access_token');
        $authCorp->setTokenExpireTime(new \DateTime('2024-12-31'));
        $authCorp->setAuthInfo([
            'agent' => [
                [
                    'agentid' => 1000001,
                    'name' => '应用1',
                    'square_logo_url' => 'https://example.com/logo1.png'
                ],
                [
                    'agentid' => 1000002,
                    'name' => '应用2',
                    'square_logo_url' => 'https://example.com/logo2.png'
                ]
            ]
        ]);

        $existingCorp = new Corp();
        $existingCorp->setCorpId('multi_corp_id');

        // 模拟存储库行为
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'multi_corp_id'])
            ->willReturn($existingCorp);

        $this->agentRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturn(null);

        // 模拟实体管理器行为 - 1次企业 + 2次应用
        $this->entityManager->expects($this->exactly(3))
            ->method('persist');
        $this->entityManager->expects($this->exactly(3))
            ->method('flush');

        // 执行方法
        $result = $this->providerService->syncAuthCorpToCorpAndAgent($authCorp);

        // 验证结果
        $this->assertSame($existingCorp, $result);
        $this->assertSame('多应用企业', $existingCorp->getName());
    }

    public function testSyncAuthCorpToCorpAndAgentWithEmptyAuthInfo(): void
    {
        // 准备数据
        $authCorp = new AuthCorp();
        $authCorp->setCorpId('empty_corp_id');
        $authCorp->setCorpName('空授权企业');
        $authCorp->setAuthInfo([]); // 空的授权信息

        $existingCorp = new Corp();
        $existingCorp->setCorpId('empty_corp_id');

        // 模拟存储库行为
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'empty_corp_id'])
            ->willReturn($existingCorp);

        // 因为没有应用信息，所以不会调用应用相关的方法
        $this->agentRepository->expects($this->never())
            ->method('findOneBy');

        // 只会持久化企业，不会持久化应用
        $this->entityManager->expects($this->once())
            ->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush');

        // 执行方法
        $result = $this->providerService->syncAuthCorpToCorpAndAgent($authCorp);

        // 验证结果
        $this->assertSame($existingCorp, $result);
        $this->assertSame('空授权企业', $existingCorp->getName());
    }

    public function testSyncAuthCorpToCorpAndAgentWithDirectAgentArray(): void
    {
        // 准备数据 - authInfo 直接是代理数组，没有 'agent' 键
        $authCorp = new AuthCorp();
        $authCorp->setCorpId('direct_corp_id');
        $authCorp->setCorpName('直接格式企业');
        $authCorp->setAccessToken('direct_access_token');
        $authCorp->setTokenExpireTime(new \DateTime('2024-12-31'));
        $authCorp->setAuthInfo([
            [
                'agentid' => 1000001,
                'name' => '直接应用',
                'square_logo_url' => 'https://example.com/direct_logo.png'
            ]
        ]);

        $existingCorp = new Corp();
        $existingCorp->setCorpId('direct_corp_id');

        // 模拟存储库行为
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'direct_corp_id'])
            ->willReturn($existingCorp);

        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        // 模拟实体管理器行为
        $this->entityManager->expects($this->exactly(2))
            ->method('persist');
        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        // 执行方法
        $result = $this->providerService->syncAuthCorpToCorpAndAgent($authCorp);

        // 验证结果
        $this->assertSame($existingCorp, $result);
        $this->assertSame('直接格式企业', $existingCorp->getName());
    }
} 