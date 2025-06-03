<?php

namespace WechatWorkProviderBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\AccessTokenAware;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Entity\CorpServerMessage;
use WechatWorkProviderBundle\Entity\Suite;

class AuthCorpTest extends TestCase
{
    private AuthCorp $authCorp;

    protected function setUp(): void
    {
        $this->authCorp = new AuthCorp();
    }

    public function testAuthCorpImplementsAccessTokenAware(): void
    {
        $this->assertInstanceOf(AccessTokenAware::class, $this->authCorp);
    }

    public function testAuthCorpIsStringable(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->authCorp);
    }

    public function testToStringWithoutId(): void
    {
        $result = (string) $this->authCorp;
        $this->assertSame('', $result);
    }

    public function testToStringWithCorpName(): void
    {
        $corpName = '测试企业';
        $this->authCorp->setCorpName($corpName);
        
        // 由于没有ID时返回空字符串，我们需要模拟有ID的情况
        $reflection = new \ReflectionClass($this->authCorp);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->authCorp, '123456789');
        
        $result = (string) $this->authCorp;
        $this->assertSame($corpName, $result);
    }

    public function testCorpIdGetterAndSetter(): void
    {
        $corpId = 'test_corp_id_123';
        $this->authCorp->setCorpId($corpId);
        $this->assertSame($corpId, $this->authCorp->getCorpId());
    }

    public function testCorpNameGetterAndSetter(): void
    {
        $corpName = '测试企业名称';
        $this->authCorp->setCorpName($corpName);
        $this->assertSame($corpName, $this->authCorp->getCorpName());
    }

    public function testCorpTypeGetterAndSetter(): void
    {
        $corpType = 'enterprise';
        $this->authCorp->setCorpType($corpType);
        $this->assertSame($corpType, $this->authCorp->getCorpType());
        
        // 测试 null 值
        $this->authCorp->setCorpType(null);
        $this->assertNull($this->authCorp->getCorpType());
    }

    public function testCorpSquareLogoUrlGetterAndSetter(): void
    {
        $logoUrl = 'https://example.com/logo.png';
        $this->authCorp->setCorpSquareLogoUrl($logoUrl);
        $this->assertSame($logoUrl, $this->authCorp->getCorpSquareLogoUrl());
        
        // 测试 null 值
        $this->authCorp->setCorpSquareLogoUrl(null);
        $this->assertNull($this->authCorp->getCorpSquareLogoUrl());
    }

    public function testCorpUserMaxGetterAndSetter(): void
    {
        $userMax = 1000;
        $this->authCorp->setCorpUserMax($userMax);
        $this->assertSame($userMax, $this->authCorp->getCorpUserMax());
        
        // 测试边界值
        $this->authCorp->setCorpUserMax(0);
        $this->assertSame(0, $this->authCorp->getCorpUserMax());
        
        // 测试 null 值
        $this->authCorp->setCorpUserMax(null);
        $this->assertNull($this->authCorp->getCorpUserMax());
    }

    public function testCorpFullNameGetterAndSetter(): void
    {
        $fullName = '测试企业有限公司';
        $this->authCorp->setCorpFullName($fullName);
        $this->assertSame($fullName, $this->authCorp->getCorpFullName());
    }

    public function testSubjectTypeGetterAndSetter(): void
    {
        $subjectType = 'enterprise';
        $this->authCorp->setSubjectType($subjectType);
        $this->assertSame($subjectType, $this->authCorp->getSubjectType());
    }

    public function testCorpScaleGetterAndSetter(): void
    {
        $scale = '100-500人';
        $this->authCorp->setCorpScale($scale);
        $this->assertSame($scale, $this->authCorp->getCorpScale());
    }

    public function testCorpIndustryGetterAndSetter(): void
    {
        $industry = 'IT软件';
        $this->authCorp->setCorpIndustry($industry);
        $this->assertSame($industry, $this->authCorp->getCorpIndustry());
    }

    public function testCorpSubIndustryGetterAndSetter(): void
    {
        $subIndustry = '软件开发';
        $this->authCorp->setCorpSubIndustry($subIndustry);
        $this->assertSame($subIndustry, $this->authCorp->getCorpSubIndustry());
    }

    public function testAuthInfoGetterAndSetter(): void
    {
        $authInfo = [
            'agent' => [
                ['agentid' => 1000001, 'name' => '测试应用']
            ]
        ];
        $this->authCorp->setAuthInfo($authInfo);
        $this->assertSame($authInfo, $this->authCorp->getAuthInfo());
        
        // 测试 null 值
        $this->authCorp->setAuthInfo(null);
        $this->assertSame([], $this->authCorp->getAuthInfo());
    }

    public function testAuthUserInfoGetterAndSetter(): void
    {
        $authUserInfo = [
            'userid' => 'admin',
            'name' => '管理员'
        ];
        $this->authCorp->setAuthUserInfo($authUserInfo);
        $this->assertSame($authUserInfo, $this->authCorp->getAuthUserInfo());
        
        // 测试 null 值
        $this->authCorp->setAuthUserInfo(null);
        $this->assertSame([], $this->authCorp->getAuthUserInfo());
    }

    public function testDealerCorpInfoGetterAndSetter(): void
    {
        $dealerInfo = ['dealer_corp_id' => 'dealer123'];
        $this->authCorp->setDealerCorpInfo($dealerInfo);
        $this->assertSame($dealerInfo, $this->authCorp->getDealerCorpInfo());
        
        // 测试 null 值
        $this->authCorp->setDealerCorpInfo(null);
        $this->assertSame([], $this->authCorp->getDealerCorpInfo());
    }

    public function testRegisterCodeInfoGetterAndSetter(): void
    {
        $registerInfo = ['register_code' => 'reg123'];
        $this->authCorp->setRegisterCodeInfo($registerInfo);
        $this->assertSame($registerInfo, $this->authCorp->getRegisterCodeInfo());
        
        // 测试 null 值
        $this->authCorp->setRegisterCodeInfo(null);
        $this->assertSame([], $this->authCorp->getRegisterCodeInfo());
    }

    public function testStateGetterAndSetter(): void
    {
        $state = 'install_state_123';
        $this->authCorp->setState($state);
        $this->assertSame($state, $this->authCorp->getState());
    }

    public function testPermanentCodeGetterAndSetter(): void
    {
        $permanentCode = 'permanent_code_abc123';
        $this->authCorp->setPermanentCode($permanentCode);
        $this->assertSame($permanentCode, $this->authCorp->getPermanentCode());
    }

    public function testAccessTokenGetterAndSetter(): void
    {
        $accessToken = 'access_token_xyz789';
        $this->authCorp->setAccessToken($accessToken);
        $this->assertSame($accessToken, $this->authCorp->getAccessToken());
    }

    public function testTokenExpireTimeGetterAndSetter(): void
    {
        $expireTime = new \DateTime('2024-12-31 23:59:59');
        $this->authCorp->setTokenExpireTime($expireTime);
        $this->assertSame($expireTime, $this->authCorp->getTokenExpireTime());
        
        // 测试 null 值
        $this->authCorp->setTokenExpireTime(null);
        $this->assertNull($this->authCorp->getTokenExpireTime());
    }

    public function testSuiteGetterAndSetter(): void
    {
        $suite = new Suite();
        $this->authCorp->setSuite($suite);
        $this->assertSame($suite, $this->authCorp->getSuite());
        
        // 测试 null 值
        $this->authCorp->setSuite(null);
        $this->assertNull($this->authCorp->getSuite());
    }

    public function testTokenGetterAndSetter(): void
    {
        $token = 'callback_token_123';
        $this->authCorp->setToken($token);
        $this->assertSame($token, $this->authCorp->getToken());
    }

    public function testEncodingAesKeyGetterAndSetter(): void
    {
        $encodingAesKey = 'encoding_aes_key_abc123def456';
        $this->authCorp->setEncodingAesKey($encodingAesKey);
        $this->assertSame($encodingAesKey, $this->authCorp->getEncodingAesKey());
    }

    public function testServerMessagesCollection(): void
    {
        // 测试初始化时集合为空
        $this->assertCount(0, $this->authCorp->getServerMessages());
        
        // 测试添加消息
        $message1 = new CorpServerMessage();
        $this->authCorp->addServerMessage($message1);
        $this->assertCount(1, $this->authCorp->getServerMessages());
        $this->assertTrue($this->authCorp->getServerMessages()->contains($message1));
        $this->assertSame($this->authCorp, $message1->getAuthCorp());
        
        // 测试添加重复消息
        $this->authCorp->addServerMessage($message1);
        $this->assertCount(1, $this->authCorp->getServerMessages());
        
        // 测试添加第二个消息
        $message2 = new CorpServerMessage();
        $this->authCorp->addServerMessage($message2);
        $this->assertCount(2, $this->authCorp->getServerMessages());
        
        // 测试移除消息
        $this->authCorp->removeServerMessage($message1);
        $this->assertCount(1, $this->authCorp->getServerMessages());
        $this->assertFalse($this->authCorp->getServerMessages()->contains($message1));
        $this->assertNull($message1->getAuthCorp());
        
        // 测试移除不存在的消息
        $message3 = new CorpServerMessage();
        $this->authCorp->removeServerMessage($message3);
        $this->assertCount(1, $this->authCorp->getServerMessages());
    }

    public function testCreateTimeGetterAndSetter(): void
    {
        $createTime = new \DateTime('2024-01-01 10:00:00');
        $this->authCorp->setCreateTime($createTime);
        $this->assertSame($createTime, $this->authCorp->getCreateTime());
        
        // 测试 null 值
        $this->authCorp->setCreateTime(null);
        $this->assertNull($this->authCorp->getCreateTime());
    }

    public function testUpdateTimeGetterAndSetter(): void
    {
        $updateTime = new \DateTime('2024-01-01 10:30:00');
        $this->authCorp->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $this->authCorp->getUpdateTime());
        
        // 测试 null 值
        $this->authCorp->setUpdateTime(null);
        $this->assertNull($this->authCorp->getUpdateTime());
    }

    public function testFluentInterface(): void
    {
        // 测试方法链式调用
        $result = $this->authCorp
            ->setCorpId('test_corp')
            ->setCorpName('测试企业')
            ->setCorpType('enterprise')
            ->setAccessToken('token123');
            
        $this->assertSame($this->authCorp, $result);
        $this->assertSame('test_corp', $this->authCorp->getCorpId());
        $this->assertSame('测试企业', $this->authCorp->getCorpName());
        $this->assertSame('enterprise', $this->authCorp->getCorpType());
        $this->assertSame('token123', $this->authCorp->getAccessToken());
    }

    public function testDefaultArrayValues(): void
    {
        // 测试默认的数组字段值
        $this->assertSame([], $this->authCorp->getAuthInfo());
        $this->assertSame([], $this->authCorp->getAuthUserInfo());
        $this->assertSame([], $this->authCorp->getDealerCorpInfo());
        $this->assertSame([], $this->authCorp->getRegisterCodeInfo());
    }
} 