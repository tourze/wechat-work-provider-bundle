<?php

namespace WechatWorkProviderBundle\Request;

class GetPermanentCodeRequest extends WithSuiteRequest
{
    private string $authCode;

    public function getRequestPath(): string
    {
        return '/cgi-bin/service/get_permanent_code';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'auth_code' => $this->getAuthCode(),
            ],
        ];
    }

    public function getAuthCode(): string
    {
        return $this->authCode;
    }

    public function setAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }
}
