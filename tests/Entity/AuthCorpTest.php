<?php

namespace WechatWorkProviderBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatWorkBundle\Entity\AccessTokenAware;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Entity\CorpServerMessage;
use WechatWorkProviderBundle\Entity\Suite;

/**
 * @internal
 */
#[CoversClass(AuthCorp::class)]
final class AuthCorpTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new AuthCorp();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'authInfo' => ['authInfo', ['key' => 'value']],
            'authUserInfo' => ['authUserInfo', ['key' => 'value']],
            'dealerCorpInfo' => ['dealerCorpInfo', ['key' => 'value']],
            'registerCodeInfo' => ['registerCodeInfo', ['key' => 'value']],
        ];
    }

    public function testAuthCorpImplementsAccessTokenAware(): void
    {
        $authCorp = new AuthCorp();
        $this->assertInstanceOf(AccessTokenAware::class, $authCorp);
    }

    public function testAuthCorpIsStringable(): void
    {
        $authCorp = new AuthCorp();
        $this->assertInstanceOf(\Stringable::class, $authCorp);
    }

    public function testToStringWithoutId(): void
    {
        $authCorp = new AuthCorp();
        $result = (string) $authCorp;
        $this->assertSame('', $result);
    }

    public function testToStringWithCorpName(): void
    {
        $authCorp = new AuthCorp();
        $corpName = '测试企业';
        $authCorp->setCorpName($corpName);

        // 由于没有ID时返回空字符串，我们需要模拟有ID的情况
        $reflection = new \ReflectionClass($authCorp);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($authCorp, '123456789');

        $result = (string) $authCorp;
        $this->assertSame($corpName, $result);
    }

    public function testCorpIdGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $corpId = 'test_corp_id_123';
        $authCorp->setCorpId($corpId);
        $this->assertSame($corpId, $authCorp->getCorpId());
    }

    public function testCorpNameGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $corpName = '测试企业名称';
        $authCorp->setCorpName($corpName);
        $this->assertSame($corpName, $authCorp->getCorpName());
    }

    public function testCorpTypeGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $corpType = 'enterprise';
        $authCorp->setCorpType($corpType);
        $this->assertSame($corpType, $authCorp->getCorpType());

        // 测试 null 值
        $authCorp->setCorpType(null);
        $this->assertNull($authCorp->getCorpType());
    }

    public function testCorpSquareLogoUrlGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $logoUrl = 'https://example.com/logo.png';
        $authCorp->setCorpSquareLogoUrl($logoUrl);
        $this->assertSame($logoUrl, $authCorp->getCorpSquareLogoUrl());

        // 测试 null 值
        $authCorp->setCorpSquareLogoUrl(null);
        $this->assertNull($authCorp->getCorpSquareLogoUrl());
    }

    public function testCorpUserMaxGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $userMax = 1000;
        $authCorp->setCorpUserMax($userMax);
        $this->assertSame($userMax, $authCorp->getCorpUserMax());

        // 测试边界值
        $authCorp->setCorpUserMax(0);
        $this->assertSame(0, $authCorp->getCorpUserMax());

        // 测试 null 值
        $authCorp->setCorpUserMax(null);
        $this->assertNull($authCorp->getCorpUserMax());
    }

    public function testCorpFullNameGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $fullName = '测试企业有限公司';
        $authCorp->setCorpFullName($fullName);
        $this->assertSame($fullName, $authCorp->getCorpFullName());
    }

    public function testSubjectTypeGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $subjectType = 'enterprise';
        $authCorp->setSubjectType($subjectType);
        $this->assertSame($subjectType, $authCorp->getSubjectType());
    }

    public function testCorpScaleGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $scale = '100-500人';
        $authCorp->setCorpScale($scale);
        $this->assertSame($scale, $authCorp->getCorpScale());
    }

    public function testCorpIndustryGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $industry = 'IT软件';
        $authCorp->setCorpIndustry($industry);
        $this->assertSame($industry, $authCorp->getCorpIndustry());
    }

    public function testCorpSubIndustryGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $subIndustry = '软件开发';
        $authCorp->setCorpSubIndustry($subIndustry);
        $this->assertSame($subIndustry, $authCorp->getCorpSubIndustry());
    }

    public function testAuthInfoGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $authInfo = [
            'agent' => [
                ['agentid' => 1000001, 'name' => '测试应用'],
            ],
        ];
        $authCorp->setAuthInfo($authInfo);
        $this->assertSame($authInfo, $authCorp->getAuthInfo());

        // 测试 null 值
        $authCorp->setAuthInfo(null);
        $this->assertSame([], $authCorp->getAuthInfo());
    }

    public function testAuthUserInfoGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $authUserInfo = [
            'userid' => 'admin',
            'name' => '管理员',
        ];
        $authCorp->setAuthUserInfo($authUserInfo);
        $this->assertSame($authUserInfo, $authCorp->getAuthUserInfo());

        // 测试 null 值
        $authCorp->setAuthUserInfo(null);
        $this->assertSame([], $authCorp->getAuthUserInfo());
    }

    public function testDealerCorpInfoGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $dealerInfo = ['dealer_corp_id' => 'dealer123'];
        $authCorp->setDealerCorpInfo($dealerInfo);
        $this->assertSame($dealerInfo, $authCorp->getDealerCorpInfo());

        // 测试 null 值
        $authCorp->setDealerCorpInfo(null);
        $this->assertSame([], $authCorp->getDealerCorpInfo());
    }

    public function testRegisterCodeInfoGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $registerInfo = ['register_code' => 'reg123'];
        $authCorp->setRegisterCodeInfo($registerInfo);
        $this->assertSame($registerInfo, $authCorp->getRegisterCodeInfo());

        // 测试 null 值
        $authCorp->setRegisterCodeInfo(null);
        $this->assertSame([], $authCorp->getRegisterCodeInfo());
    }

    public function testStateGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $state = 'install_state_123';
        $authCorp->setState($state);
        $this->assertSame($state, $authCorp->getState());
    }

    public function testPermanentCodeGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $permanentCode = 'permanent_code_abc123';
        $authCorp->setPermanentCode($permanentCode);
        $this->assertSame($permanentCode, $authCorp->getPermanentCode());
    }

    public function testAccessTokenGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $accessToken = 'access_token_xyz789';
        $authCorp->setAccessToken($accessToken);
        $this->assertSame($accessToken, $authCorp->getAccessToken());
    }

    public function testTokenExpireTimeGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $expireTime = new \DateTimeImmutable('2024-12-31 23:59:59');
        $authCorp->setTokenExpireTime($expireTime);
        $this->assertSame($expireTime, $authCorp->getTokenExpireTime());

        // 测试 null 值
        $authCorp->setTokenExpireTime(null);
        $this->assertNull($authCorp->getTokenExpireTime());
    }

    public function testSuiteGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $suite = new Suite();
        $authCorp->setSuite($suite);
        $this->assertSame($suite, $authCorp->getSuite());

        // 测试 null 值
        $authCorp->setSuite(null);
        $this->assertNull($authCorp->getSuite());
    }

    public function testTokenGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $token = 'callback_token_123';
        $authCorp->setToken($token);
        $this->assertSame($token, $authCorp->getToken());
    }

    public function testEncodingAesKeyGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $encodingAesKey = 'encoding_aes_key_abc123def456';
        $authCorp->setEncodingAesKey($encodingAesKey);
        $this->assertSame($encodingAesKey, $authCorp->getEncodingAesKey());
    }

    public function testServerMessagesCollection(): void
    {
        $authCorp = new AuthCorp();
        // 测试初始化时集合为空
        $this->assertCount(0, $authCorp->getServerMessages());

        // 测试添加消息
        $message1 = new CorpServerMessage();
        $authCorp->addServerMessage($message1);
        $this->assertCount(1, $authCorp->getServerMessages());
        $this->assertTrue($authCorp->getServerMessages()->contains($message1));
        $this->assertSame($authCorp, $message1->getAuthCorp());

        // 测试添加重复消息
        $authCorp->addServerMessage($message1);
        $this->assertCount(1, $authCorp->getServerMessages());

        // 测试添加第二个消息
        $message2 = new CorpServerMessage();
        $authCorp->addServerMessage($message2);
        $this->assertCount(2, $authCorp->getServerMessages());

        // 测试移除消息
        $authCorp->removeServerMessage($message1);
        $this->assertCount(1, $authCorp->getServerMessages());
        $this->assertFalse($authCorp->getServerMessages()->contains($message1));
        $this->assertNull($message1->getAuthCorp());

        // 测试移除不存在的消息
        $message3 = new CorpServerMessage();
        $authCorp->removeServerMessage($message3);
        $this->assertCount(1, $authCorp->getServerMessages());
    }

    public function testCreateTimeGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $createTime = new \DateTimeImmutable('2024-01-01 10:00:00');
        $authCorp->setCreateTime($createTime);
        $this->assertSame($createTime, $authCorp->getCreateTime());

        // 测试 null 值
        $authCorp->setCreateTime(null);
        $this->assertNull($authCorp->getCreateTime());
    }

    public function testUpdateTimeGetterAndSetter(): void
    {
        $authCorp = new AuthCorp();
        $updateTime = new \DateTimeImmutable('2024-01-01 10:30:00');
        $authCorp->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $authCorp->getUpdateTime());

        // 测试 null 值
        $authCorp->setUpdateTime(null);
        $this->assertNull($authCorp->getUpdateTime());
    }

    public function testFluentInterface(): void
    {
        $authCorp = new AuthCorp();
        // setter方法已改为void返回，不再支持链式调用
        $authCorp->setCorpId('test_corp');
        $authCorp->setCorpName('测试企业');
        $authCorp->setCorpType('enterprise');
        $authCorp->setAccessToken('token123');

        $this->assertSame('test_corp', $authCorp->getCorpId());
        $this->assertSame('测试企业', $authCorp->getCorpName());
        $this->assertSame('enterprise', $authCorp->getCorpType());
        $this->assertSame('token123', $authCorp->getAccessToken());
    }

    public function testDefaultArrayValues(): void
    {
        $authCorp = new AuthCorp();
        // 测试默认的数组字段值
        $this->assertSame([], $authCorp->getAuthInfo());
        $this->assertSame([], $authCorp->getAuthUserInfo());
        $this->assertSame([], $authCorp->getDealerCorpInfo());
        $this->assertSame([], $authCorp->getRegisterCodeInfo());
    }
}
