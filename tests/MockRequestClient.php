<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Tests;

use HttpClientBundle\Request\RequestInterface;
use WechatWorkProviderBundle\Service\RequestClientInterface;

/**
 * Mock实现，用于测试环境.
 */
final class MockRequestClient implements RequestClientInterface
{
    /**
     * @return mixed
     */
    public function request(RequestInterface $request)
    {
        // 返回模拟的响应数据，避免实际网络请求
        return [
            'suite_access_token' => 'mock_suite_access_token',
            'access_token' => 'mock_access_token',
            'provider_access_token' => 'mock_provider_access_token',
            'expires_in' => 7200,
        ];
    }
}
