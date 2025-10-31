<?php

namespace WechatWorkProviderBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Entity\ProviderServerMessage;
use WechatWorkProviderBundle\Entity\Suite;

/**
 * @internal
 */
#[CoversClass(Provider::class)]
final class ProviderTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Provider();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'providerSecret' => ['providerSecret', 'test_provider_secret_123'];
        yield 'providerAccessToken' => ['providerAccessToken', 'test_access_token_456'];
        yield 'tokenExpireTime' => ['tokenExpireTime', new \DateTimeImmutable('2024-12-31 23:59:59')];
        yield 'ticketExpireTime' => ['ticketExpireTime', new \DateTimeImmutable('2024-11-30 12:00:00')];
        yield 'token' => ['token', 'test_callback_token'];
        yield 'encodingAesKey' => ['encodingAesKey', 'test_encoding_aes_key_128'];
    }

    public function testProviderIsStringable(): void
    {
        $provider = new Provider();
        $this->assertInstanceOf(\Stringable::class, $provider);
    }

    public function testToStringWithoutCorpId(): void
    {
        $provider = new Provider();
        $result = (string) $provider;
        $this->assertSame('', $result);
    }

    public function testToStringWithCorpId(): void
    {
        $provider = new Provider();
        $corpId = 'test_corp_id_123';
        $provider->setCorpId($corpId);

        $result = (string) $provider;
        $this->assertSame($corpId, $result);
    }

    public function testProviderCorpIdGetterAndSetter(): void
    {
        $provider = new Provider();
        $corpId = 'test_corp_id_123';
        $provider->setCorpId($corpId);
        $this->assertSame($corpId, $provider->getCorpId());
    }

    public function testProviderSecretGetterAndSetter(): void
    {
        $provider = new Provider();
        $secret = 'test_provider_secret_123';
        $provider->setProviderSecret($secret);
        $this->assertSame($secret, $provider->getProviderSecret());
    }

    public function testProviderAccessTokenGetterAndSetter(): void
    {
        $provider = new Provider();
        $accessToken = 'test_access_token_456';
        $provider->setProviderAccessToken($accessToken);
        $this->assertSame($accessToken, $provider->getProviderAccessToken());
    }

    public function testTokenExpireTimeGetterAndSetter(): void
    {
        $provider = new Provider();
        $expireTime = new \DateTimeImmutable('2024-12-31 23:59:59');
        $provider->setTokenExpireTime($expireTime);
        $this->assertSame($expireTime, $provider->getTokenExpireTime());

        // 测试 null 值
        $provider->setTokenExpireTime(null);
        $this->assertNull($provider->getTokenExpireTime());
    }

    public function testTicketExpireTimeGetterAndSetter(): void
    {
        $provider = new Provider();
        $expireTime = new \DateTimeImmutable('2024-11-30 12:00:00');
        $provider->setTicketExpireTime($expireTime);
        $this->assertSame($expireTime, $provider->getTicketExpireTime());

        // 测试 null 值
        $provider->setTicketExpireTime(null);
        $this->assertNull($provider->getTicketExpireTime());
    }

    public function testTokenGetterAndSetter(): void
    {
        $provider = new Provider();
        $token = 'test_callback_token';
        $provider->setToken($token);
        $this->assertSame($token, $provider->getToken());
    }

    public function testEncodingAesKeyGetterAndSetter(): void
    {
        $provider = new Provider();
        $encodingAesKey = 'test_encoding_aes_key_128';
        $provider->setEncodingAesKey($encodingAesKey);
        $this->assertSame($encodingAesKey, $provider->getEncodingAesKey());
    }

    public function testSuitesCollection(): void
    {
        $provider = new Provider();
        // 测试初始化时集合为空
        $this->assertCount(0, $provider->getSuites());

        // 测试添加套件
        $suite1 = new Suite();
        $provider->addSuite($suite1);
        $this->assertCount(1, $provider->getSuites());
        $this->assertTrue($provider->getSuites()->contains($suite1));
        $this->assertSame($provider, $suite1->getProvider());

        // 测试添加重复套件
        $provider->addSuite($suite1);
        $this->assertCount(1, $provider->getSuites());

        // 测试添加第二个套件
        $suite2 = new Suite();
        $provider->addSuite($suite2);
        $this->assertCount(2, $provider->getSuites());

        // 测试移除套件
        $provider->removeSuite($suite1);
        $this->assertCount(1, $provider->getSuites());
        $this->assertFalse($provider->getSuites()->contains($suite1));
        $this->assertNull($suite1->getProvider());

        // 测试移除不存在的套件
        $suite3 = new Suite();
        $provider->removeSuite($suite3);
        $this->assertCount(1, $provider->getSuites());
    }

    public function testServerMessagesCollection(): void
    {
        $provider = new Provider();
        // 测试初始化时集合为空
        $this->assertCount(0, $provider->getServerMessages());

        // 测试添加消息
        $message1 = new ProviderServerMessage();
        $provider->addServerMessage($message1);
        $this->assertCount(1, $provider->getServerMessages());
        $this->assertTrue($provider->getServerMessages()->contains($message1));
        $this->assertSame($provider, $message1->getProvider());

        // 测试添加重复消息
        $provider->addServerMessage($message1);
        $this->assertCount(1, $provider->getServerMessages());

        // 测试添加第二个消息
        $message2 = new ProviderServerMessage();
        $provider->addServerMessage($message2);
        $this->assertCount(2, $provider->getServerMessages());

        // 测试移除消息
        $provider->removeServerMessage($message1);
        $this->assertCount(1, $provider->getServerMessages());
        $this->assertFalse($provider->getServerMessages()->contains($message1));
        $this->assertNull($message1->getProvider());

        // 测试移除不存在的消息
        $message3 = new ProviderServerMessage();
        $provider->removeServerMessage($message3);
        $this->assertCount(1, $provider->getServerMessages());
    }

    public function testCreateTimeGetterAndSetter(): void
    {
        $provider = new Provider();
        $createTime = new \DateTimeImmutable('2024-01-01 10:00:00');
        $provider->setCreateTime($createTime);
        $this->assertSame($createTime, $provider->getCreateTime());

        // 测试 null 值
        $provider->setCreateTime(null);
        $this->assertNull($provider->getCreateTime());
    }

    public function testUpdateTimeGetterAndSetter(): void
    {
        $provider = new Provider();
        $updateTime = new \DateTimeImmutable('2024-01-01 10:30:00');
        $provider->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $provider->getUpdateTime());

        // 测试 null 值
        $provider->setUpdateTime(null);
        $this->assertNull($provider->getUpdateTime());
    }

    public function testFluentInterface(): void
    {
        $provider = new Provider();
        // setter方法已改为void返回，不再支持链式调用
        $provider->setCorpId('test_corp');
        $provider->setProviderSecret('test_secret');
        $provider->setProviderAccessToken('test_token');
        $provider->setToken('test_callback_token');

        $this->assertSame('test_corp', $provider->getCorpId());
        $this->assertSame('test_secret', $provider->getProviderSecret());
        $this->assertSame('test_token', $provider->getProviderAccessToken());
        $this->assertSame('test_callback_token', $provider->getToken());
    }

    public function testNonFluentInterfaceMethods(): void
    {
        $provider = new Provider();
        // 测试返回 void 的方法
        $suite = new Suite();
        $provider->addSuite($suite);
        $this->assertCount(1, $provider->getSuites());

        $message = new ProviderServerMessage();
        $provider->addServerMessage($message);
        $this->assertCount(1, $provider->getServerMessages());
    }

    public function testIdGetter(): void
    {
        $provider = new Provider();
        $this->assertNull($provider->getId());
    }

    public function testSpecialCharactersInStringFields(): void
    {
        $provider = new Provider();

        // 测试特殊字符
        $specialChars = '测试@#$%^&*()_+-={}[]|:";\'<>?,./ 中文';
        $provider->setCorpId($specialChars);
        $provider->setProviderSecret($specialChars);
        $provider->setToken($specialChars);
        $provider->setEncodingAesKey($specialChars);

        $this->assertSame($specialChars, $provider->getCorpId());
        $this->assertSame($specialChars, $provider->getProviderSecret());
        $this->assertSame($specialChars, $provider->getToken());
        $this->assertSame($specialChars, $provider->getEncodingAesKey());
    }

    public function testLongStringValues(): void
    {
        $provider = new Provider();

        // 测试长字符串
        $longString = str_repeat('a', 1000);
        $provider->setProviderSecret($longString);
        $provider->setProviderAccessToken($longString);
        $provider->setToken($longString);
        $provider->setEncodingAesKey($longString);

        $this->assertSame($longString, $provider->getProviderSecret());
        $this->assertSame($longString, $provider->getProviderAccessToken());
        $this->assertSame($longString, $provider->getToken());
        $this->assertSame($longString, $provider->getEncodingAesKey());
    }
}
