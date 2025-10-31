<?php

namespace WechatWorkProviderBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
    private int $id = 0;

    /**
     * @var array<mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '上下文'])]
    #[Assert\Type(type: 'array')]
    private ?array $context = [];

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '原始数据'])]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65535)]
    private ?string $rawData = null;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'serverMessages')]
    private ?Provider $provider = null;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array<mixed>|null
     */
    public function getContext(): ?array
    {
        return $this->context;
    }

    /**
     * @param array<mixed>|null $context
     */
    public function setContext(?array $context): void
    {
        $this->context = $context;
    }

    public function getRawData(): ?string
    {
        return $this->rawData;
    }

    public function setRawData(?string $rawData): void
    {
        $this->rawData = $rawData;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): void
    {
        $this->provider = $provider;
    }

    public function __toString(): string
    {
        return sprintf('%s #%s', 'ProviderServerMessage', $this->id > 0 ? $this->id : 'new');
    }
}
