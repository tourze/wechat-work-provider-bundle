<?php

namespace WechatWorkProviderBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Entity\Suite;
use WechatWorkProviderBundle\Entity\ProviderServerMessage;

class ProviderTest extends TestCase
{
    private Provider $provider;

    protected function setUp(): void
    {
        $this->provider = new Provider();
    }

    public function testProviderIsStringable(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->provider);
    }

    public function testToStringWithoutCorpId(): void
    {
        $result = (string) $this->provider;
        $this->assertSame('', $result);
    }

    public function testToStringWithCorpId(): void
    {
        $corpId = 'test_corp_id_123';
        $this->provider->setCorpId($corpId);
        
        $result = (string) $this->provider;
        $this->assertSame($corpId, $result);
    }

    public function testCorpIdGetterAndSetter(): void
    {
        $corpId = 'provider_corp_id_123';
        $this->provider->setCorpId($corpId);
        $this->assertSame($corpId, $this->provider->getCorpId());
        
        // 测试 null 值
        $this->provider->setCorpId(null);
        $this->assertNull($this->provider->getCorpId());
    }

    public function testProviderSecretGetterAndSetter(): void
    {
        $providerSecret = 'provider_secret_abc123def456';
        $this->provider->setProviderSecret($providerSecret);
        $this->assertSame($providerSecret, $this->provider->getProviderSecret());
    }

    public function testProviderAccessTokenGetterAndSetter(): void
    {
        $accessToken = 'provider_access_token_xyz789';
        $this->provider->setProviderAccessToken($accessToken);
        $this->assertSame($accessToken, $this->provider->getProviderAccessToken());
        
        // 测试 null 值
        $this->provider->setProviderAccessToken(null);
        $this->assertNull($this->provider->getProviderAccessToken());
    }

    public function testTokenExpireTimeGetterAndSetter(): void
    {
        $expireTime = new \DateTime('2024-12-31 23:59:59');
        $this->provider->setTokenExpireTime($expireTime);
        $this->assertSame($expireTime, $this->provider->getTokenExpireTime());
        
        // 测试 null 值
        $this->provider->setTokenExpireTime(null);
        $this->assertNull($this->provider->getTokenExpireTime());
    }

    public function testTicketExpireTimeGetterAndSetter(): void
    {
        $ticketExpireTime = new \DateTime('2024-11-30 12:00:00');
        $this->provider->setTicketExpireTime($ticketExpireTime);
        $this->assertSame($ticketExpireTime, $this->provider->getTicketExpireTime());
        
        // 测试 null 值
        $this->provider->setTicketExpireTime(null);
        $this->assertNull($this->provider->getTicketExpireTime());
    }

    public function testTokenGetterAndSetter(): void
    {
        $token = 'callback_token_test';
        $result = $this->provider->setToken($token);
        
        // 测试链式调用
        $this->assertSame($this->provider, $result);
        $this->assertSame($token, $this->provider->getToken());
        
        // 测试 null 值
        $this->provider->setToken(null);
        $this->assertNull($this->provider->getToken());
    }

    public function testEncodingAesKeyGetterAndSetter(): void
    {
        $encodingAesKey = 'encoding_aes_key_test_123456789';
        $result = $this->provider->setEncodingAesKey($encodingAesKey);
        
        // 测试链式调用
        $this->assertSame($this->provider, $result);
        $this->assertSame($encodingAesKey, $this->provider->getEncodingAesKey());
        
        // 测试 null 值
        $this->provider->setEncodingAesKey(null);
        $this->assertNull($this->provider->getEncodingAesKey());
    }

    public function testSuitesCollection(): void
    {
        // 测试初始化时集合为空
        $this->assertCount(0, $this->provider->getSuites());
        
        // 测试添加套件
        $suite1 = new Suite();
        $result = $this->provider->addSuite($suite1);
        
        // 测试链式调用
        $this->assertSame($this->provider, $result);
        $this->assertCount(1, $this->provider->getSuites());
        $this->assertTrue($this->provider->getSuites()->contains($suite1));
        $this->assertSame($this->provider, $suite1->getProvider());
        
        // 测试添加重复套件
        $this->provider->addSuite($suite1);
        $this->assertCount(1, $this->provider->getSuites());
        
        // 测试添加第二个套件
        $suite2 = new Suite();
        $this->provider->addSuite($suite2);
        $this->assertCount(2, $this->provider->getSuites());
        
        // 测试移除套件
        $result = $this->provider->removeSuite($suite1);
        $this->assertSame($this->provider, $result);
        $this->assertCount(1, $this->provider->getSuites());
        $this->assertFalse($this->provider->getSuites()->contains($suite1));
        $this->assertNull($suite1->getProvider());
        
        // 测试移除不存在的套件
        $suite3 = new Suite();
        $this->provider->removeSuite($suite3);
        $this->assertCount(1, $this->provider->getSuites());
    }

    public function testServerMessagesCollection(): void
    {
        // 测试初始化时集合为空
        $this->assertCount(0, $this->provider->getServerMessages());
        
        // 测试添加消息
        $message1 = new ProviderServerMessage();
        $result = $this->provider->addServerMessage($message1);
        
        // 测试链式调用
        $this->assertSame($this->provider, $result);
        $this->assertCount(1, $this->provider->getServerMessages());
        $this->assertTrue($this->provider->getServerMessages()->contains($message1));
        $this->assertSame($this->provider, $message1->getProvider());
        
        // 测试添加重复消息
        $this->provider->addServerMessage($message1);
        $this->assertCount(1, $this->provider->getServerMessages());
        
        // 测试添加第二个消息
        $message2 = new ProviderServerMessage();
        $this->provider->addServerMessage($message2);
        $this->assertCount(2, $this->provider->getServerMessages());
        
        // 测试移除消息
        $result = $this->provider->removeServerMessage($message1);
        $this->assertSame($this->provider, $result);
        $this->assertCount(1, $this->provider->getServerMessages());
        $this->assertFalse($this->provider->getServerMessages()->contains($message1));
        $this->assertNull($message1->getProvider());
        
        // 测试移除不存在的消息
        $message3 = new ProviderServerMessage();
        $this->provider->removeServerMessage($message3);
        $this->assertCount(1, $this->provider->getServerMessages());
    }

    public function testCreateTimeGetterAndSetter(): void
    {
        $createTime = new \DateTime('2024-01-01 08:00:00');
        $this->provider->setCreateTime($createTime);
        $this->assertSame($createTime, $this->provider->getCreateTime());
        
        // 测试 null 值
        $this->provider->setCreateTime(null);
        $this->assertNull($this->provider->getCreateTime());
    }

    public function testUpdateTimeGetterAndSetter(): void
    {
        $updateTime = new \DateTime('2024-01-01 09:30:00');
        $this->provider->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $this->provider->getUpdateTime());
        
        // 测试 null 值
        $this->provider->setUpdateTime(null);
        $this->assertNull($this->provider->getUpdateTime());
    }

    public function testFluentInterface(): void
    {
        // 测试支持链式调用的方法
        $result = $this->provider
            ->setProviderSecret('secret123')
            ->setProviderAccessToken('token456')
            ->setTokenExpireTime(new \DateTime('2024-12-31'))
            ->setTicketExpireTime(new \DateTime('2024-11-30'))
            ->setToken('callback_token')
            ->setEncodingAesKey('aes_key');
            
        $this->assertSame($this->provider, $result);
        $this->assertSame('secret123', $this->provider->getProviderSecret());
        $this->assertSame('token456', $this->provider->getProviderAccessToken());
        $this->assertSame('callback_token', $this->provider->getToken());
        $this->assertSame('aes_key', $this->provider->getEncodingAesKey());
    }

    public function testNonFluentInterfaceMethods(): void
    {
        // setCorpId 方法返回 void，不支持链式调用
        $this->provider->setCorpId('test_corp');
        $this->assertSame('test_corp', $this->provider->getCorpId());
        
        // setCreateTime 和 setUpdateTime 也返回 void
        $createTime = new \DateTime();
        $this->provider->setCreateTime($createTime);
        $this->assertSame($createTime, $this->provider->getCreateTime());
        
        $updateTime = new \DateTime();
        $this->provider->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $this->provider->getUpdateTime());
    }

    public function testIdGetter(): void
    {
        // ID是由生成器自动生成的，初始值为null
        $this->assertNull($this->provider->getId());
    }

    public function testSpecialCharactersInStringFields(): void
    {
        $specialCorpId = 'corp!@#$%^&*()_+-=测试';
        $specialSecret = 'secret测试中文123!@#';
        $specialToken = 'token_with_special_chars_测试';
        
        $this->provider->setCorpId($specialCorpId);
        $this->provider->setProviderSecret($specialSecret);
        $this->provider->setToken($specialToken);
        
        $this->assertSame($specialCorpId, $this->provider->getCorpId());
        $this->assertSame($specialSecret, $this->provider->getProviderSecret());
        $this->assertSame($specialToken, $this->provider->getToken());
        
        // 测试 __toString() 方法
        $this->assertSame($specialCorpId, (string) $this->provider);
    }

    public function testLongStringValues(): void
    {
        $longCorpId = str_repeat('a', 64);
        $longSecret = str_repeat('b', 200);
        $longToken = str_repeat('c', 40);
        $longAesKey = str_repeat('d', 128);
        
        $this->provider->setCorpId($longCorpId);
        $this->provider->setProviderSecret($longSecret);
        $this->provider->setToken($longToken);
        $this->provider->setEncodingAesKey($longAesKey);
        
        $this->assertSame($longCorpId, $this->provider->getCorpId());
        $this->assertSame($longSecret, $this->provider->getProviderSecret());
        $this->assertSame($longToken, $this->provider->getToken());
        $this->assertSame($longAesKey, $this->provider->getEncodingAesKey());
    }
} 