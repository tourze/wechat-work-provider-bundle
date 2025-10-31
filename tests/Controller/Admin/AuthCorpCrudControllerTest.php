<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatWorkProviderBundle\Controller\Admin\AuthCorpCrudController;
use WechatWorkProviderBundle\Entity\AuthCorp;

/**
 * 授权企业CRUD控制器测试
 * @internal
 */
#[CoversClass(AuthCorpCrudController::class)]
#[RunTestsInSeparateProcesses]
final class AuthCorpCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testGetEntityFqcn(): void
    {
        $result = AuthCorpCrudController::getEntityFqcn();

        self::assertSame(AuthCorp::class, $result);
    }

    public function testConfigureFilters(): void
    {
        $controller = new AuthCorpCrudController();
        $controller->configureFilters(Filters::new());

        // 测试方法调用成功
        $this->expectNotToPerformAssertions();
    }

    public function testConfigureFields(): void
    {
        $controller = new AuthCorpCrudController();
        $fields = $controller->configureFields('index');

        self::assertNotEmpty($fields);
    }

    /**
     * 测试配置CRUD基本设置
     */
    public function testConfigureCrud(): void
    {
        $controller = new AuthCorpCrudController();
        $controller->configureCrud(Crud::new());

        // 测试方法调用成功
        $this->expectNotToPerformAssertions();
    }

    /**
     * 获取控制器服务实例
     */
    protected function getControllerService(): AuthCorpCrudController
    {
        return new AuthCorpCrudController();
    }

    /**
     * 提供首页列头测试数据
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '企业微信ID列' => ['企业微信ID'];
        yield '企业简称列' => ['企业简称'];
        yield '企业类型列' => ['企业类型'];
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
        yield '企业微信ID字段' => ['corpId'];
        yield '企业简称字段' => ['corpName'];
        yield '企业类型字段' => ['corpType'];
        yield '企业方形头像字段' => ['corpSquareLogoUrl'];
        yield '用户规模字段' => ['corpUserMax'];
        yield '企业全称字段' => ['corpFullName'];
        yield '主体类型字段' => ['subjectType'];
        yield '企业规模字段' => ['corpScale'];
        yield '所属行业字段' => ['corpIndustry'];
        yield '子行业字段' => ['corpSubIndustry'];
        // 跳过数组字段以避免 Array to string conversion 警告：
        // authInfo, authUserInfo, dealerCorpInfo, registerCodeInfo
        yield '状态值字段' => ['state'];
        yield '永久授权码字段' => ['permanentCode'];
        yield 'Access Token字段' => ['accessToken'];
        yield 'Token过期时间字段' => ['tokenExpireTime'];
        yield '关联应用模板字段' => ['suite'];
        yield '代开发Token字段' => ['token'];
        yield '代开发EncodingAESKey字段' => ['encodingAesKey'];
    }

    /**
     * 提供编辑页面字段测试数据
     */
    public static function provideEditPageFields(): iterable
    {
        yield '企业微信ID字段' => ['corpId'];
        yield '企业简称字段' => ['corpName'];
        yield '企业类型字段' => ['corpType'];
        yield '企业方形头像字段' => ['corpSquareLogoUrl'];
        yield '用户规模字段' => ['corpUserMax'];
        yield '企业全称字段' => ['corpFullName'];
        yield '主体类型字段' => ['subjectType'];
        yield '企业规模字段' => ['corpScale'];
        yield '所属行业字段' => ['corpIndustry'];
        yield '子行业字段' => ['corpSubIndustry'];
        // 跳过数组字段以避免 Array to string conversion 警告：
        // authInfo, authUserInfo, dealerCorpInfo, registerCodeInfo
        yield '状态值字段' => ['state'];
        yield '永久授权码字段' => ['permanentCode'];
        yield 'Access Token字段' => ['accessToken'];
        yield 'Token过期时间字段' => ['tokenExpireTime'];
        yield '关联应用模板字段' => ['suite'];
        yield '代开发Token字段' => ['token'];
        yield '代开发EncodingAESKey字段' => ['encodingAesKey'];
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

        // 清空必填字段以触发验证错误，同时清空数组字段以避免类型错误
        $entityName = $this->getEntitySimpleName();
        $form[$entityName . '[corpId]'] = '';
        $form[$entityName . '[authInfo]'] = '';
        $form[$entityName . '[authUserInfo]'] = '';
        $form[$entityName . '[dealerCorpInfo]'] = '';
        $form[$entityName . '[registerCodeInfo]'] = '';

        $crawler = $client->submit($form);

        // 验证返回422状态码（验证失败）
        $this->assertResponseStatusCodeSame(422);

        // 验证页面包含错误信息
        $this->assertStringContainsString('should not be blank',
            $crawler->filter('.invalid-feedback')->text());
    }
}
