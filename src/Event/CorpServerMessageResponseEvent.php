<?php

namespace WechatWorkProviderBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Entity\CorpServerMessage;

class CorpServerMessageResponseEvent extends Event
{
    /**
     * @var array<string, mixed>
     */
    private array $responseData = [];

    private readonly AuthCorp $authCorp;

    public function __construct(
        private readonly CorpServerMessage $message,
    ) {
        $authCorp = $message->getAuthCorp();
        if (null === $authCorp) {
            throw new \InvalidArgumentException('AuthCorp cannot be null in CorpServerMessage');
        }
        $this->authCorp = $authCorp;
    }

    public function getMessage(): CorpServerMessage
    {
        return $this->message;
    }

    public function getAuthCorp(): AuthCorp
    {
        return $this->authCorp;
    }

    /**
     * @return array<string, mixed>
     */
    public function getResponseData(): array
    {
        return $this->responseData;
    }

    /**
     * @param array<string, mixed> $responseData
     */
    public function setResponseData(array $responseData): void
    {
        $this->responseData = $responseData;
    }
}
