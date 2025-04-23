<?php

namespace WechatWorkProviderBundle\Request;

use HttpClientBundle\Request\ApiRequest;

/**
 * 获取服务商凭证
 *
 * @see https://developer.work.weixin.qq.com/document/path/96237
 */
class GetProviderTokenRequest extends ApiRequest
{
    /**
     * @var string 服务商的corpid
     */
    private string $corpId;

    /**
     * @var string 服务商的secret，在服务商管理后台可见
     */
    private string $providerSecret;

    public function getRequestPath(): string
    {
        return '/cgi-bin/service/get_provider_token';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'corpid' => $this->getCorpId(),
                'provider_secret' => $this->getProviderSecret(),
            ],
        ];
    }

    public function getCorpId(): string
    {
        return $this->corpId;
    }

    public function setCorpId(string $corpId): void
    {
        $this->corpId = $corpId;
    }

    public function getProviderSecret(): string
    {
        return $this->providerSecret;
    }

    public function setProviderSecret(string $providerSecret): void
    {
        $this->providerSecret = $providerSecret;
    }
}
