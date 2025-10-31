<?php

namespace WechatWorkProviderBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkProviderBundle\EventSubscriber\SuiteListener;

/**
 * @internal
 */
#[CoversClass(SuiteListener::class)]
#[RunTestsInSeparateProcesses]
final class SuiteListenerTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // EventSubscriber 测试的自定义初始化逻辑
    }

    public function testListenerCanBeInstantiated(): void
    {
        $listener = self::getService(SuiteListener::class);
        $this->assertInstanceOf(SuiteListener::class, $listener);
    }

    public function testIsEntityListener(): void
    {
        $reflection = new \ReflectionClass(SuiteListener::class);
        $attributes = $reflection->getAttributes();
        $hasAsEntityListenerAttribute = false;

        foreach ($attributes as $attribute) {
            if ('Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener' === $attribute->getName()) {
                $hasAsEntityListenerAttribute = true;
                break;
            }
        }

        $this->assertTrue($hasAsEntityListenerAttribute);
    }

    public function testUpdateSuiteTicket(): void
    {
        $reflection = new \ReflectionClass(SuiteListener::class);
        $this->assertTrue($reflection->hasMethod('updateSuiteTicket'));
    }
}
