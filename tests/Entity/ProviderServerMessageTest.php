<?php

namespace WechatWorkProviderBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatWorkProviderBundle\Entity\ProviderServerMessage;

/**
 * @internal
 */
#[CoversClass(ProviderServerMessage::class)]
final class ProviderServerMessageTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ProviderServerMessage();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'context' => ['context', ['test' => 'value'], ['new' => 'data']],
            'rawData' => ['rawData', 'raw data string', 'new raw data'],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testEntityCanBeInstantiated(): void
    {
        $entity = new ProviderServerMessage();
        $this->assertInstanceOf(ProviderServerMessage::class, $entity);
    }
}
