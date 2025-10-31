<?php

namespace WechatWorkProviderBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatWorkProviderBundle\Entity\CorpServerMessage;

/**
 * @internal
 */
#[CoversClass(CorpServerMessage::class)]
final class CorpServerMessageTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new CorpServerMessage();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'decryptData' => ['decryptData', ['key' => 'value']],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testEntityCanBeInstantiated(): void
    {
        $entity = new CorpServerMessage();
        $this->assertInstanceOf(CorpServerMessage::class, $entity);
    }
}
