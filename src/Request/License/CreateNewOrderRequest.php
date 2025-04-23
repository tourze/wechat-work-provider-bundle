<?php

namespace WechatWorkProviderBundle\Request\License;

use WechatWorkProviderBundle\Request\WithSuiteRequest;

/**
 * 下单购买帐号
 *
 * @see https://developer.work.weixin.qq.com/document/path/97182
 */
class CreateNewOrderRequest extends WithSuiteRequest
{
    public function getRequestPath(): string
    {
        return '/cgi-bin/license/create_new_order';
    }

    public function getRequestOptions(): ?array
    {
        $json = [];

        return [
            'json' => $json,
        ];
    }
}
