<?php

namespace WechatWorkProviderBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatWorkProviderBundle\Repository\SuiteServerMessageRepository;

#[AsPermission(title: '应用模板回调')]
#[ORM\Entity(repositoryClass: SuiteServerMessageRepository::class)]
#[ORM\Table(name: 'wechat_work_suite_server_message', options: ['comment' => '应用模板回调'])]
class SuiteServerMessage
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '上下文'])]
    private ?array $context = [];

    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    /**
     * 这里存储的是反序列后又序列化的原始数据.
     */
    #[Keyword]
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

    public function setCreateTime(?\DateTimeInterface $createdAt): self
    {
        $this->createTime = $createdAt;

        return $this;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
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
}
