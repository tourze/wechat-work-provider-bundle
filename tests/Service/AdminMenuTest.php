<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatWorkProviderBundle\Service\AdminMenu;

/**
 * 管理后台菜单服务测试
 *
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // Setup for AdminMenu tests
    }

    public function testServiceImplementsMenuProviderInterface(): void
    {
        $container = self::getContainer();
        $adminMenu = $container->get(AdminMenu::class);
        $this->assertInstanceOf(MenuProviderInterface::class, $adminMenu);
    }

    public function testServiceClassShouldBeFinal(): void
    {
        $reflection = new \ReflectionClass(AdminMenu::class);
        $this->assertTrue($reflection->isFinal());
    }

    public function testInvokeAddsMenuItems(): void
    {
        $container = self::getContainer();
        /** @var AdminMenu $adminMenu */
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu->__invoke($rootItem);

        // 验证菜单结构
        $wechatMenu = $rootItem->getChild('企业微信管理');
        self::assertNotNull($wechatMenu, '企业微信管理菜单应该存在');

        $providerMenu = $wechatMenu->getChild('服务商管理');
        self::assertNotNull($providerMenu, '服务商管理菜单应该存在');

        // 验证子菜单
        self::assertNotNull($providerMenu->getChild('服务商配置'), '服务商配置菜单应该存在');
        self::assertNotNull($providerMenu->getChild('应用模板管理'), '应用模板管理菜单应该存在');
        self::assertNotNull($providerMenu->getChild('授权企业管理'), '授权企业管理菜单应该存在');

        // 验证消息回调管理菜单
        $messageMenu = $providerMenu->getChild('消息回调管理');
        self::assertNotNull($messageMenu, '消息回调管理菜单应该存在');
        self::assertNotNull($messageMenu->getChild('服务商回调消息'), '服务商回调消息菜单应该存在');
        self::assertNotNull($messageMenu->getChild('应用模板回调消息'), '应用模板回调消息菜单应该存在');
        self::assertNotNull($messageMenu->getChild('企业回调消息'), '企业回调消息菜单应该存在');
    }

    public function testMenuItemsHaveIcons(): void
    {
        $container = self::getContainer();
        /** @var AdminMenu $adminMenu */
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu->__invoke($rootItem);

        $wechatMenu = $rootItem->getChild('企业微信管理');
        self::assertNotNull($wechatMenu);

        $providerMenu = $wechatMenu->getChild('服务商管理');
        self::assertNotNull($providerMenu);

        // 检查图标属性
        self::assertSame('fab fa-weixin', $wechatMenu->getAttribute('icon'));
        self::assertSame('fas fa-server', $providerMenu->getAttribute('icon'));

        $configMenu = $providerMenu->getChild('服务商配置');
        self::assertNotNull($configMenu);
        self::assertSame('fas fa-cog', $configMenu->getAttribute('icon'));

        $templateMenu = $providerMenu->getChild('应用模板管理');
        self::assertNotNull($templateMenu);
        self::assertSame('fas fa-puzzle-piece', $templateMenu->getAttribute('icon'));

        $corpMenu = $providerMenu->getChild('授权企业管理');
        self::assertNotNull($corpMenu);
        self::assertSame('fas fa-building', $corpMenu->getAttribute('icon'));

        // 检查消息回调菜单图标
        $messageMenu = $providerMenu->getChild('消息回调管理');
        self::assertNotNull($messageMenu);
        self::assertSame('fas fa-comments', $messageMenu->getAttribute('icon'));

        $providerCallbackMenu = $messageMenu->getChild('服务商回调消息');
        self::assertNotNull($providerCallbackMenu);
        self::assertSame('fas fa-envelope', $providerCallbackMenu->getAttribute('icon'));

        $suiteCallbackMenu = $messageMenu->getChild('应用模板回调消息');
        self::assertNotNull($suiteCallbackMenu);
        self::assertSame('fas fa-envelope-open', $suiteCallbackMenu->getAttribute('icon'));

        $corpCallbackMenu = $messageMenu->getChild('企业回调消息');
        self::assertNotNull($corpCallbackMenu);
        self::assertSame('fas fa-mail-bulk', $corpCallbackMenu->getAttribute('icon'));
    }
}
