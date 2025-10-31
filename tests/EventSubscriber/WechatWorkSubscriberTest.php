<?php

namespace WechatWorkProviderBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;
use WechatWorkProviderBundle\EventSubscriber\WechatWorkSubscriber;

/**
 * @internal
 */
#[CoversClass(WechatWorkSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class WechatWorkSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
        // EventSubscriber 测试的自定义初始化逻辑
    }

    public function testSubscriberCanBeInstantiated(): void
    {
        $subscriber = self::getService(WechatWorkSubscriber::class);
        $this->assertInstanceOf(WechatWorkSubscriber::class, $subscriber);
    }

    public function testIsEventListener(): void
    {
        $reflection = new \ReflectionClass(WechatWorkSubscriber::class);
        $methods = $reflection->getMethods();
        $hasAsEventListenerAttribute = false;

        foreach ($methods as $method) {
            $attributes = $method->getAttributes();
            foreach ($attributes as $attribute) {
                if ('Symfony\Component\EventDispatcher\Attribute\AsEventListener' === $attribute->getName()) {
                    $hasAsEventListenerAttribute = true;
                    break 2;
                }
            }
        }

        $this->assertTrue($hasAsEventListenerAttribute);
    }

    public function testOnCorpServerMessageResponse(): void
    {
        $reflection = new \ReflectionClass(WechatWorkSubscriber::class);
        $this->assertTrue($reflection->hasMethod('onCorpServerMessageResponse'));
    }
}
