<?php

namespace WechatWorkProviderBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkProviderBundle\Entity\Provider;

abstract class WithProviderRequest extends ApiRequest
{
    private Provider $provider;

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function setProvider(Provider $provider): void
    {
        $this->provider = $provider;
    }
}
