<?php

namespace WechatWorkProviderBundle\Request\Batch;

use HttpClientBundle\Request\AutoRetryRequest;
use WechatWorkProviderBundle\Request\WithAuthCorpRequest;

/**
 * userid的转换
 *
 * @see https://developer.work.weixin.qq.com/document/path/97106
 */
class UserIdToOpenUserIdRequest extends WithAuthCorpRequest implements AutoRetryRequest
{
    /**
     * @var array<string>
     */
    private array $userIdList = [];

    public function getMaxRetries(): int
    {
        return 3;
    }

    public function getRequestPath(): string
    {
        return '/cgi-bin/batch/userid_to_openuserid';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'userid_list' => $this->getUserIdList(),
            ],
        ];
    }

    /**
     * @return array<string>
     */
    public function getUserIdList(): array
    {
        return $this->userIdList;
    }

    /**
     * @param array<string> $userIdList
     */
    public function setUserIdList(array $userIdList): void
    {
        $this->userIdList = $userIdList;
    }
}
