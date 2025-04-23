<?php

namespace WechatWorkProviderBundle\Request\License;

use HttpClientBundle\Request\AutoRetryRequest;
use WechatWorkProviderBundle\Request\WithProviderRequest;

/**
 * 获取订单中的帐号列表
 *
 * @see https://developer.work.weixin.qq.com/document/path/97186
 */
class GetOrderAccountListRequest extends WithProviderRequest implements AutoRetryRequest
{
    /**
     * @var string 订单号
     */
    private string $orderId;

    /**
     * @var int 返回的最大记录数，整型，最大值1000，默认值500
     */
    private int $limit = 500;

    /**
     * @var string|null 用于分页查询的游标，字符串类型，由上一次调用返回，首次调用可不填
     */
    private ?string $cursor = null;

    public function getMaxRetries(): int
    {
        return 3;
    }

    public function getRequestPath(): string
    {
        return '/cgi-bin/license/list_order_account';
    }

    public function getRequestOptions(): ?array
    {
        $json = [
            'order_id' => $this->getOrderId(),
            'limit' => $this->getLimit(),
        ];
        if (null !== $this->getCursor()) {
            $json['cursor'] = $this->getCursor();
        }

        return [
            'json' => $json,
        ];
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getCursor(): ?string
    {
        return $this->cursor;
    }

    public function setCursor(?string $cursor): void
    {
        $this->cursor = $cursor;
    }
}
