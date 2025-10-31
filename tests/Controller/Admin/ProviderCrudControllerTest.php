<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatWorkProviderBundle\Controller\Admin\ProviderCrudController;
use WechatWorkProviderBundle\Entity\Provider;

/**
 * 服务商CRUD控制器测试
 * @internal
 */
#[CoversClass(ProviderCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ProviderCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testGetEntityFqcn(): void
    {
        $result = ProviderCrudController::getEntityFqcn();

        self::assertSame(Provider::class, $result);
    }

    public function testConfigureFilters(): void
    {
        $controller = new ProviderCrudController();
        $controller->configureFilters(Filters::new());

        // 测试方法调用成功
        $this->expectNotToPerformAssertions();
    }

    public function testConfigureFields(): void
    {
        $controller = new ProviderCrudController();
        $fields = $controller->configureFields('index');

        self::assertNotEmpty($fields);
    }

    /**
     * 测试配置CRUD基本设置
     */
    public function testConfigureCrud(): void
    {
        $controller = new ProviderCrudController();
        $controller->configureCrud(Crud::new());

        // 测试方法调用成功
        $this->expectNotToPerformAssertions();
    }

    /**
     * 获取控制器服务实例
     */
    protected function getControllerService(): ProviderCrudController
    {
        return new ProviderCrudController();
    }

    /**
     * 提供首页列头测试数据
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '服务商企业ID列' => ['服务商企业ID'];
        yield '关联应用模板列' => ['关联应用模板'];
        yield '服务器消息列' => ['服务器消息'];
        yield '创建时间列' => ['创建时间'];
        yield '更新时间列' => ['更新时间'];
    }

    /**
     * 提供新建页面字段测试数据
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield '服务商企业ID字段' => ['corpId'];
        yield '服务商Secret字段' => ['providerSecret'];
        yield '服务商AccessToken字段' => ['providerAccessToken'];
        yield 'Token过期时间字段' => ['tokenExpireTime'];
        yield 'Ticket过期时间字段' => ['ticketExpireTime'];
        yield '回调Token字段' => ['token'];
        yield '回调加密密钥字段' => ['encodingAesKey'];
    }

    /**
     * 提供编辑页面字段测试数据
     */
    public static function provideEditPageFields(): iterable
    {
        yield '服务商企业ID字段' => ['corpId'];
        yield '服务商Secret字段' => ['providerSecret'];
        yield '服务商AccessToken字段' => ['providerAccessToken'];
        yield 'Token过期时间字段' => ['tokenExpireTime'];
        yield 'Ticket过期时间字段' => ['ticketExpireTime'];
        yield '回调Token字段' => ['token'];
        yield '回调加密密钥字段' => ['encodingAesKey'];
    }

    /**
     * 测试验证错误
     */
    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        // 访问新建页面
        $crawler = $client->request('GET', $this->generateAdminUrl('new'));

        // 获取表单并提交空数据
        $form = $crawler->selectButton('Create')->form();

        // 清空必填字段以触发验证错误
        $entityName = $this->getEntitySimpleName();
        $form[$entityName . '[corpId]'] = '';
        $form[$entityName . '[providerSecret]'] = '';

        $crawler = $client->submit($form);

        // 验证返回422状态码（验证失败）
        $this->assertResponseStatusCodeSame(422);

        // 验证页面包含错误信息
        $this->assertStringContainsString('should not be blank',
            $crawler->filter('.invalid-feedback')->text());
    }
}
