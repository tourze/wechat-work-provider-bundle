<?php

namespace WechatWorkProviderBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '企业微信服务商')]
class WechatWorkProviderBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \WechatWorkBundle\WechatWorkBundle::class => ['all' => true],
            \WechatWorkExternalContactBundle\WechatWorkExternalContactBundle::class => ['all' => true],
        ];
    }
}
