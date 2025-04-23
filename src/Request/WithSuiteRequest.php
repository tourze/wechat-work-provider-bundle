<?php

namespace WechatWorkProviderBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkProviderBundle\Entity\Suite;

abstract class WithSuiteRequest extends ApiRequest
{
    private Suite $suite;

    public function getSuite(): Suite
    {
        return $this->suite;
    }

    public function setSuite(Suite $suite): void
    {
        $this->suite = $suite;
    }
}
