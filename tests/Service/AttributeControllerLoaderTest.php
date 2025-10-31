<?php

namespace WechatWorkProviderBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkProviderBundle\Service\AttributeControllerLoader;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // Service 测试的自定义初始化逻辑
    }

    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
    }

    public function testAutoload(): void
    {
        $reflection = new \ReflectionClass(AttributeControllerLoader::class);
        $this->assertTrue($reflection->hasMethod('autoload'));
    }

    public function testLoad(): void
    {
        $reflection = new \ReflectionClass(AttributeControllerLoader::class);
        $this->assertTrue($reflection->hasMethod('load'));
    }

    public function testSupports(): void
    {
        $reflection = new \ReflectionClass(AttributeControllerLoader::class);
        $this->assertTrue($reflection->hasMethod('supports'));
    }
}
