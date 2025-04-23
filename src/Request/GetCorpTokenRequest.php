<?php

namespace WechatWorkProviderBundle\Request;

use HttpClientBundle\Request\ApiRequest;

class GetCorpTokenRequest extends ApiRequest
{
    /**
     * @var string 授权方corpid
     */
    private string $authCorpId;

    /**
     * @var string 永久授权码，通过get_permanent_code获取
     */
    private string $permanentCode;

    public function getRequestPath(): string
    {
        return '/cgi-bin/gettoken';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'query' => [
                'corpid' => $this->getAuthCorpId(),
                'corpsecret' => $this->getPermanentCode(),
            ],
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }

    public function getAuthCorpId(): string
    {
        return $this->authCorpId;
    }

    public function setAuthCorpId(string $authCorpId): void
    {
        $this->authCorpId = $authCorpId;
    }

    public function getPermanentCode(): string
    {
        return $this->permanentCode;
    }

    public function setPermanentCode(string $permanentCode): void
    {
        $this->permanentCode = $permanentCode;
    }
}
