<?php

namespace WechatWorkProviderBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;
use WechatWorkProviderBundle\Repository\SuiteServerMessageRepository;

#[ORM\Entity(repositoryClass: SuiteServerMessageRepository::class)]
#[ORM\Table(name: 'wechat_work_suite_server_message', options: ['comment' => '应用模板回调'])]
class SuiteServerMessage implements \Stringable
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
    private ?Suite $suite = null;

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

    public function getSuite(): ?Suite
    {
        return $this->suite;
    }

    public function setSuite(?Suite $suite): static
    {
        $this->suite = $suite;

        return $this;
    }


    public function __toString(): string
    {
        return sprintf('%s #%s', 'SuiteServerMessage', $this->id ?? 'new');
    }
}
