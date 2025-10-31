<?php

namespace WechatWorkProviderBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle;
use WechatWorkBundle\WechatWorkBundle;

class WechatWorkProviderBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            RoutingAutoLoaderBundle::class => ['all' => true],
            DoctrineBundle::class => ['all' => true],
            WechatWorkBundle::class => ['all' => true],
        ];
    }
}
