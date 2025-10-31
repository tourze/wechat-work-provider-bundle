<?php

namespace WechatWorkProviderBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatWorkProviderBundle\Entity\Suite;

/**
 * @internal
 */
#[CoversClass(Suite::class)]
final class SuiteTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Suite();
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
        yield 'suiteId' => ['suiteId', 'test_suite_id_123'];
        yield 'suiteSecret' => ['suiteSecret', 'test_suite_secret_456'];
        yield 'suiteTicket' => ['suiteTicket', 'test_suite_ticket_789'];
        yield 'suiteAccessToken' => ['suiteAccessToken', 'test_suite_access_token'];
        yield 'tokenExpireTime' => ['tokenExpireTime', new \DateTimeImmutable('2024-12-31 23:59:59')];
        yield 'ticketExpireTime' => ['ticketExpireTime', new \DateTimeImmutable('2024-11-30 12:00:00')];
        yield 'token' => ['token', 'test_callback_token'];
        yield 'encodingAesKey' => ['encodingAesKey', 'test_encoding_aes_key_120'];
    }

    public function testEntityCanBeInstantiated(): void
    {
        $entity = new Suite();
        $this->assertInstanceOf(Suite::class, $entity);
    }
}
