<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Service;

use HttpClientBundle\Request\RequestInterface;

interface RequestClientInterface
{
    /**
     * @return mixed
     */
    public function request(RequestInterface $request);
}
