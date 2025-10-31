<?php

namespace WechatWorkProviderBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkProviderBundle\EventSubscriber\AuthCorpListener;

/**
 * @internal
 */
#[CoversClass(AuthCorpListener::class)]
#[RunTestsInSeparateProcesses]
final class AuthCorpListenerTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // EventSubscriber 测试的自定义初始化逻辑
    }

    public function testListenerCanBeInstantiated(): void
    {
        $listener = self::getService(AuthCorpListener::class);
        $this->assertInstanceOf(AuthCorpListener::class, $listener);
    }

    public function testIsEntityListener(): void
    {
        $reflection = new \ReflectionClass(AuthCorpListener::class);
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

    public function testAutoCreateAuthCorp(): void
    {
        $reflection = new \ReflectionClass(AuthCorpListener::class);
        $this->assertTrue($reflection->hasMethod('autoCreateAuthCorp'));
    }
}
