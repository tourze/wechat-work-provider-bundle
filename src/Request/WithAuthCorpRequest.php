<?php

namespace WechatWorkProviderBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkProviderBundle\Entity\AuthCorp;

abstract class WithAuthCorpRequest extends ApiRequest
{
    private AuthCorp $authCorp;

    public function getAuthCorp(): AuthCorp
    {
        return $this->authCorp;
    }

    public function setAuthCorp(AuthCorp $authCorp): void
    {
        $this->authCorp = $authCorp;
    }
}
