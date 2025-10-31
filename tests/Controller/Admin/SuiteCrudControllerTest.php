<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatWorkProviderBundle\Controller\Admin\SuiteCrudController;
use WechatWorkProviderBundle\Entity\Suite;

/**
 * 应用模板CRUD控制器测试
 * @internal
 */
#[CoversClass(SuiteCrudController::class)]
#[RunTestsInSeparateProcesses]
final class SuiteCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testGetEntityFqcn(): void
    {
        $result = SuiteCrudController::getEntityFqcn();

        self::assertSame(Suite::class, $result);
    }

    public function testConfigureFilters(): void
    {
        $controller = new SuiteCrudController();
        $controller->configureFilters(Filters::new());

        // 测试方法调用成功
        $this->expectNotToPerformAssertions();
    }

    public function testConfigureFields(): void
    {
        $controller = new SuiteCrudController();
        $fields = $controller->configureFields('index');

        self::assertNotEmpty($fields);
    }

    /**
     * 测试配置CRUD基本设置
     */
    public function testConfigureCrud(): void
    {
        $controller = new SuiteCrudController();
        $controller->configureCrud(Crud::new());

        // 测试方法调用成功
        $this->expectNotToPerformAssertions();
    }

    /**
     * 获取控制器服务实例
     */
    protected function getControllerService(): SuiteCrudController
    {
        return new SuiteCrudController();
    }

    /**
     * 提供首页列头测试数据
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '关联服务商列' => ['关联服务商'];
        yield '模板ID列' => ['模板ID'];
        yield '授权企业列' => ['授权企业'];
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
        yield '关联服务商字段' => ['provider'];
        yield '模板ID字段' => ['suiteId'];
        yield '模板Secret字段' => ['suiteSecret'];
        yield '模板Ticket字段' => ['suiteTicket'];
        yield 'AccessToken字段' => ['suiteAccessToken'];
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
        yield '关联服务商字段' => ['provider'];
        yield '模板ID字段' => ['suiteId'];
        yield '模板Secret字段' => ['suiteSecret'];
        yield '模板Ticket字段' => ['suiteTicket'];
        yield 'AccessToken字段' => ['suiteAccessToken'];
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
        $form['Suite[suiteId]'] = '';
        $form['Suite[suiteSecret]'] = '';

        $crawler = $client->submit($form);

        // 验证返回422状态码（验证失败）
        $this->assertResponseStatusCodeSame(422);

        // 验证页面包含错误信息
        $this->assertStringContainsString(
            'should not be blank',
            $crawler->filter('.invalid-feedback')->text()
        );
    }
}
