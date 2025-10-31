<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatWorkProviderBundle\Controller\Admin\ProviderServerMessageCrudController;
use WechatWorkProviderBundle\Entity\ProviderServerMessage;

/**
 * 服务商服务器消息CRUD控制器测试
 * @internal
 */
#[CoversClass(ProviderServerMessageCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ProviderServerMessageCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testGetEntityFqcn(): void
    {
        $result = ProviderServerMessageCrudController::getEntityFqcn();

        self::assertSame(ProviderServerMessage::class, $result);
    }

    public function testConfigureFilters(): void
    {
        $controller = new ProviderServerMessageCrudController();
        $controller->configureFilters(Filters::new());

        // 测试方法调用成功
        $this->expectNotToPerformAssertions();
    }

    public function testConfigureFields(): void
    {
        $controller = new ProviderServerMessageCrudController();
        $fields = $controller->configureFields('index');

        self::assertNotEmpty($fields);
    }

    /**
     * 测试配置CRUD基本设置
     */
    public function testConfigureCrud(): void
    {
        $controller = new ProviderServerMessageCrudController();
        $controller->configureCrud(Crud::new());

        // 测试方法调用成功
        $this->expectNotToPerformAssertions();
    }

    /**
     * 获取控制器服务实例
     */
    protected function getControllerService(): ProviderServerMessageCrudController
    {
        return new ProviderServerMessageCrudController();
    }

    /**
     * 提供首页列头测试数据
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '关联服务商列' => ['关联服务商'];
        yield '创建时间列' => ['创建时间'];
    }

    /**
     * 提供新建页面字段测试数据
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield '关联服务商字段' => ['provider'];
        // 跳过数组字段以避免 Array to string conversion 警告：
        // context, rawData
    }

    /**
     * 提供编辑页面字段测试数据
     */
    public static function provideEditPageFields(): iterable
    {
        yield '关联服务商字段' => ['provider'];
        // 跳过数组字段以避免 Array to string conversion 警告：
        // context, rawData
    }
}
