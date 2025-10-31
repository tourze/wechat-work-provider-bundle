<?php

namespace WechatWorkProviderBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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

    #[ORM\ManyToOne(targetEntity: Suite::class, inversedBy: 'serverMessages')]
    private ?Suite $suite = null;

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

    public function getSuite(): ?Suite
    {
        return $this->suite;
    }

    public function setSuite(?Suite $suite): void
    {
        $this->suite = $suite;
    }

    public function __toString(): string
    {
        return sprintf('%s #%s', 'SuiteServerMessage', $this->id > 0 ? $this->id : 'new');
    }
}
