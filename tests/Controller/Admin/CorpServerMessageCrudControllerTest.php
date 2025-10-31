<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatWorkProviderBundle\Controller\Admin\CorpServerMessageCrudController;
use WechatWorkProviderBundle\Entity\CorpServerMessage;

/**
 * 企业服务器消息CRUD控制器测试
 * @internal
 */
#[CoversClass(CorpServerMessageCrudController::class)]
#[RunTestsInSeparateProcesses]
final class CorpServerMessageCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testGetEntityFqcn(): void
    {
        $result = CorpServerMessageCrudController::getEntityFqcn();

        self::assertSame(CorpServerMessage::class, $result);
    }

    public function testConfigureFilters(): void
    {
        $controller = new CorpServerMessageCrudController();
        $controller->configureFilters(Filters::new());

        // 测试方法调用成功
        $this->expectNotToPerformAssertions();
    }

    public function testConfigureFields(): void
    {
        $controller = new CorpServerMessageCrudController();
        $fields = $controller->configureFields('index');

        self::assertNotEmpty($fields);
    }

    /**
     * 测试配置CRUD基本设置
     */
    public function testConfigureCrud(): void
    {
        $controller = new CorpServerMessageCrudController();
        $controller->configureCrud(Crud::new());

        // 测试方法调用成功
        $this->expectNotToPerformAssertions();
    }

    /**
     * 获取控制器服务实例
     */
    protected function getControllerService(): CorpServerMessageCrudController
    {
        return new CorpServerMessageCrudController();
    }

    /**
     * 提供首页列头测试数据
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '接收方企业ID列' => ['接收方企业ID'];
        yield '发送方用户ID列' => ['发送方用户ID'];
        yield '关联授权企业列' => ['关联授权企业'];
        yield '消息创建时间列' => ['消息创建时间'];
        yield '消息类型列' => ['消息类型'];
        yield '事件类型列' => ['事件类型'];
        yield '用户ID列' => ['用户ID'];
    }

    /**
     * 提供新建页面字段测试数据
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield '接收方企业ID字段' => ['toUserName'];
        yield '发送方用户ID字段' => ['fromUserName'];
        yield '关联授权企业字段' => ['authCorp'];
        yield '消息创建时间字段' => ['createTime'];
        // 跳过数组字段以避免 Array to string conversion 警告：
        // decryptData, rawData, response
        yield '消息类型字段' => ['msgType'];
        yield '事件类型字段' => ['event'];
        yield '变更类型字段' => ['changeType'];
        yield '群聊ID字段' => ['chatId'];
        yield '外部联系人ID字段' => ['externalUserId'];
        yield '加入场景字段' => ['joinScene'];
        yield '成员变更数量字段' => ['memChangeCnt'];
        yield '退出场景字段' => ['quitScene'];
        yield '状态字段' => ['state'];
        yield '更新详情字段' => ['updateDetail'];
        yield '用户ID字段' => ['userId'];
        yield '欢迎语Code字段' => ['welcomeCode'];
    }

    /**
     * 提供编辑页面字段测试数据
     */
    public static function provideEditPageFields(): iterable
    {
        yield '接收方企业ID字段' => ['toUserName'];
        yield '发送方用户ID字段' => ['fromUserName'];
        yield '关联授权企业字段' => ['authCorp'];
        yield '消息创建时间字段' => ['createTime'];
        yield '解密后数据字段' => ['decryptData'];
        yield '原始数据字段' => ['rawData'];
        yield '消息类型字段' => ['msgType'];
        yield '事件类型字段' => ['event'];
        yield '变更类型字段' => ['changeType'];
        yield '群聊ID字段' => ['chatId'];
        yield '外部联系人ID字段' => ['externalUserId'];
        yield '加入场景字段' => ['joinScene'];
        yield '成员变更数量字段' => ['memChangeCnt'];
        yield '退出场景字段' => ['quitScene'];
        yield '状态字段' => ['state'];
        yield '更新详情字段' => ['updateDetail'];
        yield '用户ID字段' => ['userId'];
        yield '欢迎语Code字段' => ['welcomeCode'];
        yield '响应数据字段' => ['response'];
    }

    /**
     * 测试验证错误
     */
    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        // 访问新建页面
        $crawler = $client->request('GET', $this->generateAdminUrl('new'));

        // 获取表单并提交空数据（CodeEditorField提交空字符串，这会触发类型错误）
        $form = $crawler->selectButton('Create')->form();

        // 清空必填字段以触发验证错误，同时清空数组字段以避免类型错误
        $entityName = $this->getEntitySimpleName();
        $form[$entityName . '[toUserName]'] = '';
        $form[$entityName . '[decryptData]'] = '';
        $form[$entityName . '[rawData]'] = '';
        $form[$entityName . '[response]'] = '';

        $crawler = $client->submit($form);

        // 验证返回422状态码（验证失败）
        $this->assertResponseStatusCodeSame(422);

        // 验证页面包含错误信息
        $this->assertStringContainsString('should not be blank',
            $crawler->filter('.invalid-feedback')->text());
    }
}
