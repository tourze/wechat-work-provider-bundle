<?php

namespace WechatWorkProviderBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '企业微信服务商')]
class WechatWorkProviderBundle extends Bundle
{
}
