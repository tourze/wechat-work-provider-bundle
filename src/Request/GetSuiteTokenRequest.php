<?php

namespace WechatWorkProviderBundle\Request;

use HttpClientBundle\Request\ApiRequest;

/**
 * @see https://developer.work.weixin.qq.com/document/path/90600
 */
class GetSuiteTokenRequest extends ApiRequest
{
    private string $suiteId;

    private string $suiteSecret;

    private string $suiteTicket;

    public function getRequestPath(): string
    {
        return '/cgi-bin/service/get_suite_token';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'suite_id' => $this->getSuiteId(),
                'suite_secret' => $this->getSuiteSecret(),
                'suite_ticket' => $this->getSuiteTicket(),
            ],
        ];
    }

    public function getSuiteId(): string
    {
        return $this->suiteId;
    }

    public function setSuiteId(string $suiteId): void
    {
        $this->suiteId = $suiteId;
    }

    public function getSuiteSecret(): string
    {
        return $this->suiteSecret;
    }

    public function setSuiteSecret(string $suiteSecret): void
    {
        $this->suiteSecret = $suiteSecret;
    }

    public function getSuiteTicket(): string
    {
        return $this->suiteTicket;
    }

    public function setSuiteTicket(string $suiteTicket): void
    {
        $this->suiteTicket = $suiteTicket;
    }
}
