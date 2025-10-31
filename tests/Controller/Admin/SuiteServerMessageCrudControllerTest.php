<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatWorkProviderBundle\Controller\Admin\SuiteServerMessageCrudController;
use WechatWorkProviderBundle\Entity\SuiteServerMessage;

/**
 * 应用模板服务器消息CRUD控制器测试
 * @internal
 */
#[CoversClass(SuiteServerMessageCrudController::class)]
#[RunTestsInSeparateProcesses]
final class SuiteServerMessageCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testGetEntityFqcn(): void
    {
        $result = SuiteServerMessageCrudController::getEntityFqcn();

        self::assertSame(SuiteServerMessage::class, $result);
    }

    public function testConfigureFilters(): void
    {
        $controller = new SuiteServerMessageCrudController();
        $controller->configureFilters(Filters::new());

        // 测试方法调用成功
        $this->expectNotToPerformAssertions();
    }

    public function testConfigureFields(): void
    {
        $controller = new SuiteServerMessageCrudController();
        $fields = $controller->configureFields('index');

        self::assertNotEmpty($fields);
    }

    /**
     * 测试配置CRUD基本设置
     */
    public function testConfigureCrud(): void
    {
        $controller = new SuiteServerMessageCrudController();
        $controller->configureCrud(Crud::new());

        // 测试方法调用成功
        $this->expectNotToPerformAssertions();
    }

    /**
     * 获取控制器服务实例
     */
    protected function getControllerService(): SuiteServerMessageCrudController
    {
        return new SuiteServerMessageCrudController();
    }

    /**
     * 提供首页列头测试数据
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '关联应用模板列' => ['关联应用模板'];
        yield '创建时间列' => ['创建时间'];
    }

    /**
     * 提供新建页面字段测试数据
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield '关联应用模板字段' => ['suite'];
        // 跳过数组字段以避免 Array to string conversion 警告：
        // context, rawData
    }

    /**
     * 提供编辑页面字段测试数据
     */
    public static function provideEditPageFields(): iterable
    {
        yield '关联应用模板字段' => ['suite'];
        // 跳过数组字段以避免 Array to string conversion 警告：
        // context, rawData
    }
}
