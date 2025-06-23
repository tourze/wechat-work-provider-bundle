<?php

namespace WechatWorkProviderBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;
use WechatWorkProviderBundle\Repository\ProviderServerMessageRepository;

#[ORM\Entity(repositoryClass: ProviderServerMessageRepository::class)]
#[ORM\Table(name: 'wechat_work_provider_server_message', options: ['comment' => '服务商回调'])]
class ProviderServerMessage implements \Stringable
{
    use CreateTimeAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '上下文'])]
    private ?array $context = [];

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '原始数据'])]
    private ?string $rawData = null;

    #[ORM\ManyToOne(inversedBy: 'serverMessages')]
    private ?Provider $provider = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContext(): ?array
    {
        return $this->context;
    }

    public function setContext(?array $context): self
    {
        $this->context = $context;

        return $this;
    }


    public function getRawData(): ?string
    {
        return $this->rawData;
    }

    public function setRawData(?string $rawData): self
    {
        $this->rawData = $rawData;

        return $this;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): static
    {
        $this->provider = $provider;

        return $this;
    }


    public function __toString(): string
    {
        return sprintf('%s #%s', 'ProviderServerMessage', $this->id ?? 'new');
    }
}
