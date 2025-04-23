<?php

namespace WechatWorkProviderBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Entity\CorpServerMessage;

class CorpServerMessageResponseEvent extends Event
{
    private CorpServerMessage $message;

    private AuthCorp $authCorp;

    public function getMessage(): CorpServerMessage
    {
        return $this->message;
    }

    public function setMessage(CorpServerMessage $message): void
    {
        $this->message = $message;
    }

    public function getAuthCorp(): AuthCorp
    {
        return $this->authCorp;
    }

    public function setAuthCorp(AuthCorp $authCorp): void
    {
        $this->authCorp = $authCorp;
    }
}
