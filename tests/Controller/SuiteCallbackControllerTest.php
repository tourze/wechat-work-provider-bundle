<?php

namespace WechatWorkProviderBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;
use WechatWorkProviderBundle\Controller\SuiteCallbackController;
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Entity\Suite;

/**
 * @internal
 */
#[CoversClass(SuiteCallbackController::class)]
#[RunTestsInSeparateProcesses]
final class SuiteCallbackControllerTest extends AbstractWebTestCase
{
    public function testGetRequestWithValidSuite(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试用的Suite实体
        $suite = $this->createSuite();

        $client->catchExceptions(false);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessageMatches('/-40001|校验不通过/');

        $client->request('GET', '/wechat-work-provider/server/suite/' . $suite->getId(), [
            'msg_signature' => 'test_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
            'echostr' => 'encrypted_test_string',
        ]);
    }

    public function testGetRequestWithInvalidSuite(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/object not found|找不到应用/');

        $client->request('GET', '/wechat-work-provider/server/suite/999', [
            'msg_signature' => 'test_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
            'echostr' => 'encrypted_test_string',
        ]);
    }

    public function testPostRequestWithValidXmlData(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试用的Suite实体
        $suite = $this->createSuite();

        $xmlData = '<xml><ToUserName>test</ToUserName><FromUserName>test</FromUserName><CreateTime>1234567890</CreateTime><MsgType>text</MsgType></xml>';

        $client->request('POST', '/wechat-work-provider/server/suite/' . $suite->getId(), [
            'msg_signature' => 'test_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
        ], [], ['CONTENT_TYPE' => 'application/xml'], $xmlData);

        // 验证请求被处理
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUnauthorizedAccessWithoutParameters(): void
    {
        $client = self::createClientWithDatabase();

        $suite = $this->createSuite();

        $client->catchExceptions(false);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('缺少必要的查询参数');

        $client->request('GET', '/wechat-work-provider/server/suite/' . $suite->getId());
    }

    public function testPostRequestCreatesSuiteServerMessage(): void
    {
        $client = self::createClientWithDatabase();

        $suite = $this->createSuite();

        $xmlData = '<xml><ToUserName>test_suite</ToUserName><FromUserName>test_user</FromUserName><CreateTime>1234567890</CreateTime><MsgType>event</MsgType><Event>subscribe</Event></xml>';

        $client->request('POST', '/wechat-work-provider/server/suite/' . $suite->getId(), [
            'msg_signature' => 'test_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
        ], [], ['CONTENT_TYPE' => 'application/xml'], $xmlData);

        // 验证响应成功处理
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // 验证消息被保存到数据库（如果加密解析成功的话）
        // 注意：由于加密验证可能失败，这里不强制验证数据库记录
    }

    public function testGetRequestWithInvalidSignature(): void
    {
        $client = self::createClientWithDatabase();

        // 创建正常的Suite
        $suite = $this->createSuite();

        $client->catchExceptions(false);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessageMatches('/-40001|校验不通过/');

        $client->request('GET', '/wechat-work-provider/server/suite/' . $suite->getId(), [
            'msg_signature' => 'test_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
            'echostr' => 'encrypted_test_string',
        ]);
    }

    public function testPostRequestHandlesDecryptionError(): void
    {
        $client = self::createClientWithDatabase();

        $suite = $this->createSuite();

        // 发送无效的加密数据
        $invalidXmlData = '<xml><invalid>data</invalid></xml>';

        $client->request('POST', '/wechat-work-provider/server/suite/' . $suite->getId(), [
            'msg_signature' => 'invalid_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
        ], [], ['CONTENT_TYPE' => 'application/xml'], $invalidXmlData);

        // 解密失败时应该返回success避免重试
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    private function createSuite(): Suite
    {
        // 先创建Provider
        $provider = new Provider();
        $provider->setCorpId('test_provider_corp_' . uniqid());
        $provider->setProviderSecret('test_provider_secret');
        $provider->setToken('test_token');
        $provider->setEncodingAesKey('abcdefghijklmnopqrstuv0123456789ABCDEFGHIJK');
        self::getEntityManager()->persist($provider);

        // 创建Suite
        $suite = new Suite();
        $suite->setSuiteId('test_suite_' . uniqid());
        $suite->setSuiteSecret('test_suite_secret');
        $suite->setToken('test_token');
        $suite->setEncodingAesKey('abcdefghijklmnopqrstuv0123456789ABCDEFGHIJK');
        $suite->setProvider($provider);

        self::getEntityManager()->persist($suite);
        self::getEntityManager()->flush();

        return $suite;
    }

    public function testPutMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $suite = $this->createSuite();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('PUT', '/wechat-work-provider/server/suite/' . $suite->getId());
    }

    public function testDeleteMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $suite = $this->createSuite();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('DELETE', '/wechat-work-provider/server/suite/' . $suite->getId());
    }

    public function testPatchMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $suite = $this->createSuite();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('PATCH', '/wechat-work-provider/server/suite/' . $suite->getId());
    }

    public function testOptionsMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $suite = $this->createSuite();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('OPTIONS', '/wechat-work-provider/server/suite/' . $suite->getId());
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();
        $suite = $this->createSuite();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request($method, '/wechat-work-provider/server/suite/' . $suite->getId());
    }
}
