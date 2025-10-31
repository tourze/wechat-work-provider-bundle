<?php

namespace WechatWorkProviderBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatWorkProviderBundle\Entity\SuiteServerMessage;

/**
 * @internal
 */
#[CoversClass(SuiteServerMessage::class)]
final class SuiteServerMessageTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new SuiteServerMessage();
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'context' => ['context', ['key' => 'value', 'test' => true]];
        yield 'rawData' => ['rawData', 'test_raw_data_string'];
    }

    public function testEntityCanBeInstantiated(): void
    {
        $entity = new SuiteServerMessage();
        $this->assertInstanceOf(SuiteServerMessage::class, $entity);
    }
}
