<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Entity\CorpServerMessage;
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Entity\ProviderServerMessage;
use WechatWorkProviderBundle\Entity\Suite;
use WechatWorkProviderBundle\Entity\SuiteServerMessage;

/**
 * 企业微信服务商管理后台菜单提供者
 */
#[Autoconfigure(public: true)]
final readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('企业微信管理')) {
            $item->addChild('企业微信管理')
                ->setAttribute('icon', 'fab fa-weixin')
            ;
        }

        $wechatWorkMenu = $item->getChild('企业微信管理');
        if (null === $wechatWorkMenu) {
            return;
        }

        // 添加服务商管理子菜单
        if (null === $wechatWorkMenu->getChild('服务商管理')) {
            $wechatWorkMenu->addChild('服务商管理')
                ->setAttribute('icon', 'fas fa-server')
            ;
        }

        $providerMenu = $wechatWorkMenu->getChild('服务商管理');
        if (null === $providerMenu) {
            return;
        }

        // 服务商基础管理
        $providerMenu->addChild('服务商配置')
            ->setUri($this->linkGenerator->getCurdListPage(Provider::class))
            ->setAttribute('icon', 'fas fa-cog')
        ;

        $providerMenu->addChild('应用模板管理')
            ->setUri($this->linkGenerator->getCurdListPage(Suite::class))
            ->setAttribute('icon', 'fas fa-puzzle-piece')
        ;

        $providerMenu->addChild('授权企业管理')
            ->setUri($this->linkGenerator->getCurdListPage(AuthCorp::class))
            ->setAttribute('icon', 'fas fa-building')
        ;

        // 消息回调管理
        if (null === $providerMenu->getChild('消息回调管理')) {
            $providerMenu->addChild('消息回调管理')
                ->setAttribute('icon', 'fas fa-comments')
            ;
        }

        $messageMenu = $providerMenu->getChild('消息回调管理');
        if (null === $messageMenu) {
            return;
        }

        $messageMenu->addChild('服务商回调消息')
            ->setUri($this->linkGenerator->getCurdListPage(ProviderServerMessage::class))
            ->setAttribute('icon', 'fas fa-envelope')
        ;

        $messageMenu->addChild('应用模板回调消息')
            ->setUri($this->linkGenerator->getCurdListPage(SuiteServerMessage::class))
            ->setAttribute('icon', 'fas fa-envelope-open')
        ;

        $messageMenu->addChild('企业回调消息')
            ->setUri($this->linkGenerator->getCurdListPage(CorpServerMessage::class))
            ->setAttribute('icon', 'fas fa-mail-bulk')
        ;
    }
}
