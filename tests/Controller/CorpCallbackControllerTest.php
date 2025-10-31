<?php

namespace WechatWorkProviderBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;
use WechatWorkProviderBundle\Controller\CorpCallbackController;
use WechatWorkProviderBundle\Entity\AuthCorp;

/**
 * @internal
 */
#[CoversClass(CorpCallbackController::class)]
#[RunTestsInSeparateProcesses]
final class CorpCallbackControllerTest extends AbstractWebTestCase
{
    public function testGetRequestWithValidCorpIdAndEchoString(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试用的AuthCorp实体
        $authCorp = $this->createAuthCorp();

        $client->catchExceptions(false);

        // 预期签名验证失败
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('-40001');

        $client->request('GET', '/wechat-work-provider/server/start/' . $authCorp->getCorpId(), [
            'msg_signature' => 'test_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
            'echostr' => 'encrypted_test_string',
        ]);
    }

    public function testGetRequestWithInvalidCorpId(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('找不到授权企业');

        $client->request('GET', '/wechat-work-provider/server/start/invalid_corp_id', [
            'msg_signature' => 'test_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
            'echostr' => 'encrypted_test_string',
        ]);
    }

    public function testPostRequestWithValidXmlData(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试用的AuthCorp实体
        $authCorp = $this->createAuthCorp();

        $xmlData = '<xml><ToUserName>test</ToUserName><FromUserName>test</FromUserName></xml>';

        $client->catchExceptions(false);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('企业回调解密失败');

        $client->request('POST', '/wechat-work-provider/server/start/' . $authCorp->getCorpId(), [
            'msg_signature' => 'test_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
        ], [], [], $xmlData);
    }

    public function testUnauthorizedAccessWithoutParameters(): void
    {
        $client = self::createClientWithDatabase();

        $authCorp = $this->createAuthCorp();

        $client->catchExceptions(false);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('缺少必要的查询参数');

        $client->request('GET', '/wechat-work-provider/server/start/' . $authCorp->getCorpId());
    }

    public function testPostRequestCreatesServerMessage(): void
    {
        $client = self::createClientWithDatabase();

        $authCorp = $this->createAuthCorp();

        $xmlData = '<xml><ToUserName>test</ToUserName><FromUserName>test</FromUserName><MsgType>text</MsgType></xml>';

        $client->catchExceptions(false);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('企业回调解密失败');

        $client->request('POST', '/wechat-work-provider/server/start/' . $authCorp->getCorpId(), [
            'msg_signature' => 'test_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
        ], [], [], $xmlData);
    }

    private function createAuthCorp(): AuthCorp
    {
        $authCorp = new AuthCorp();
        $authCorp->setCorpId('test_corp_' . uniqid());
        $authCorp->setCorpName('Test Corp');
        $authCorp->setToken('test_token');
        $authCorp->setEncodingAesKey('abcdefghijklmnopqrstuv0123456789ABCDEFGHIJK');
        $authCorp->setPermanentCode('test_permanent_code');

        self::getEntityManager()->persist($authCorp);
        self::getEntityManager()->flush();

        return $authCorp;
    }

    public function testPutMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $authCorp = $this->createAuthCorp();

        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PUT', '/wechat-work-provider/server/start/' . $authCorp->getCorpId());
    }

    public function testDeleteMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $authCorp = $this->createAuthCorp();

        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('DELETE', '/wechat-work-provider/server/start/' . $authCorp->getCorpId());
    }

    public function testPatchMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $authCorp = $this->createAuthCorp();

        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PATCH', '/wechat-work-provider/server/start/' . $authCorp->getCorpId());
    }

    public function testOptionsMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $authCorp = $this->createAuthCorp();

        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('OPTIONS', '/wechat-work-provider/server/start/' . $authCorp->getCorpId());
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();
        $authCorp = $this->createAuthCorp();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request($method, '/wechat-work-provider/server/start/' . $authCorp->getCorpId());
    }
}
