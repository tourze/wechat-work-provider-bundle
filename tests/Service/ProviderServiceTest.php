<?php

namespace WechatWorkProviderBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkProviderBundle\Service\ProviderService;

/**
 * @internal
 */
#[CoversClass(ProviderService::class)]
#[RunTestsInSeparateProcesses]
final class ProviderServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // Service 测试的自定义初始化逻辑
    }

    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(ProviderService::class);
        $this->assertInstanceOf(ProviderService::class, $service);
    }

    public function testSyncAuthCorpToCorpAndAgent(): void
    {
        $reflection = new \ReflectionClass(ProviderService::class);
        $this->assertTrue($reflection->hasMethod('syncAuthCorpToCorpAndAgent'));
    }
}
