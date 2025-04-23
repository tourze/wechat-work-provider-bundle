<?php

namespace WechatWorkProviderBundle\Request\License;

use WechatWorkProviderBundle\Request\WithProviderRequest;

/**
 * 激活帐号
 *
 * @see https://developer.work.weixin.qq.com/document/path/97188
 */
class ActiveAccountRequest extends WithProviderRequest
{
    /**
     * @var string 帐号激活码
     */
    private string $activeCode;

    /**
     * @var string 激活码所属企业corpid
     */
    private string $corpId;

    /**
     * @var string 待绑定激活的企业成员userid
     */
    private string $userId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/license/active_account';
    }

    public function getRequestOptions(): ?array
    {
        $json = [
            'active_code' => $this->getActiveCode(),
            'corpid' => $this->getCorpId(),
            'userid' => $this->getUserId(),
        ];

        return [
            'json' => $json,
        ];
    }

    public function getActiveCode(): string
    {
        return $this->activeCode;
    }

    public function setActiveCode(string $activeCode): void
    {
        $this->activeCode = $activeCode;
    }

    public function getCorpId(): string
    {
        return $this->corpId;
    }

    public function setCorpId(string $corpId): void
    {
        $this->corpId = $corpId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }
}
