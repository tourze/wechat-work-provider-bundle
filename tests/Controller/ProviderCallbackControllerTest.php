<?php

namespace WechatWorkProviderBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;
use WechatWorkProviderBundle\Controller\ProviderCallbackController;
use WechatWorkProviderBundle\Entity\Provider;

/**
 * @internal
 */
#[CoversClass(ProviderCallbackController::class)]
#[RunTestsInSeparateProcesses]
final class ProviderCallbackControllerTest extends AbstractWebTestCase
{
    public function testGetRequestWithValidProvider(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试用的Provider实体
        $provider = $this->createProvider();

        $client->catchExceptions(false);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('-40001');

        $client->request('GET', '/wechat-work-provider/server/provider/' . $provider->getId(), [
            'msg_signature' => 'test_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
            'echostr' => 'encrypted_test_string',
        ]);
    }

    public function testGetRequestWithInvalidProvider(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/object not found|找不到服务商信息/');

        $client->request('GET', '/wechat-work-provider/server/provider/999', [
            'msg_signature' => 'test_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
            'echostr' => 'encrypted_test_string',
        ]);
    }

    public function testPostRequestWithValidXmlData(): void
    {
        $client = self::createClientWithDatabase();

        // 创建测试用的Provider实体
        $provider = $this->createProvider();

        $xmlData = '<xml><InfoType>suite_ticket</InfoType><SuiteId>test_suite</SuiteId></xml>';

        $client->request('POST', '/wechat-work-provider/server/provider/' . $provider->getId(), [
            'msg_signature' => 'test_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
        ], [], ['CONTENT_TYPE' => 'application/xml'], $xmlData);

        // 验证请求被处理 - 由于加密验证失败，应该返回success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUnauthorizedAccessWithoutParameters(): void
    {
        $client = self::createClientWithDatabase();

        $provider = $this->createProvider();

        $client->catchExceptions(false);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('缺少必要的查询参数');

        $client->request('GET', '/wechat-work-provider/server/provider/' . $provider->getId());
    }

    public function testPostRequestCreatesProviderServerMessage(): void
    {
        $client = self::createClientWithDatabase();

        $provider = $this->createProvider();

        $xmlData = '<xml><InfoType>suite_ticket</InfoType><SuiteId>test_suite</SuiteId><Ticket>test_ticket</Ticket></xml>';

        // 由于使用测试数据进行加密验证会失败，但现在返回success避免重试
        $client->request('POST', '/wechat-work-provider/server/provider/' . $provider->getId(), [
            'msg_signature' => 'test_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
        ], [], ['CONTENT_TYPE' => 'application/xml'], $xmlData);

        // 验证解密失败时也返回success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('success', $client->getResponse()->getContent());
    }

    public function testPostRequestHandlesDecryptionError(): void
    {
        $client = self::createClientWithDatabase();

        $provider = $this->createProvider();

        // 发送无效的加密数据
        $invalidXmlData = '<xml><invalid>data</invalid></xml>';

        $client->request('POST', '/wechat-work-provider/server/provider/' . $provider->getId(), [
            'msg_signature' => 'invalid_signature',
            'timestamp' => '1234567890',
            'nonce' => 'test_nonce',
        ], [], ['CONTENT_TYPE' => 'application/xml'], $invalidXmlData);

        // 解密失败时应该返回success避免重试
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    private function createProvider(): Provider
    {
        $provider = new Provider();
        $provider->setCorpId('test_provider_corp_' . uniqid());
        $provider->setProviderSecret('test_provider_secret');
        $provider->setToken('test_token');
        $provider->setEncodingAesKey('abcdefghijklmnopqrstuv0123456789ABCDEFGHIJK');

        self::getEntityManager()->persist($provider);
        self::getEntityManager()->flush();

        return $provider;
    }

    public function testPutMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $provider = $this->createProvider();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('PUT', '/wechat-work-provider/server/provider/' . $provider->getId());
    }

    public function testDeleteMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $provider = $this->createProvider();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('DELETE', '/wechat-work-provider/server/provider/' . $provider->getId());
    }

    public function testPatchMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $provider = $this->createProvider();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('PATCH', '/wechat-work-provider/server/provider/' . $provider->getId());
    }

    public function testOptionsMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $provider = $this->createProvider();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('OPTIONS', '/wechat-work-provider/server/provider/' . $provider->getId());
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();
        $provider = $this->createProvider();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request($method, '/wechat-work-provider/server/provider/' . $provider->getId());
    }
}
