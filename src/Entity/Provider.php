<?php

namespace WechatWorkProviderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use WechatWorkProviderBundle\Repository\ProviderRepository;

#[Creatable]
#[Editable]
#[Deletable]
#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\Table(name: 'wechat_work_provider', options: ['comment' => '服务商'])]
class Provider implements \Stringable
{
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 64, options: ['comment' => '服务商corpId'])]
    private ?string $corpId = null;

    /**
     * @var string|null 服务商的secret，在服务商管理后台可见
     */
    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 200, options: ['comment' => '服务商secret'])]
    private ?string $providerSecret = null;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 200, nullable: true)]
    private ?string $providerAccessToken = null;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $tokenExpireTime = null;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $ticketExpireTime = null;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 40, nullable: true, options: ['comment' => 'Token'])]
    private ?string $token = null;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => 'EncodingAESKey'])]
    private ?string $encodingAesKey = null;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Suite::class)]
    private Collection $suites;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: ProviderServerMessage::class)]
    private Collection $serverMessages;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function __construct()
    {
        $this->suites = new ArrayCollection();
        $this->serverMessages = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getCorpId()) {
            return '';
        }

        return "{$this->getCorpId()}";
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getProviderSecret(): ?string
    {
        return $this->providerSecret;
    }

    public function setProviderSecret(string $providerSecret): self
    {
        $this->providerSecret = $providerSecret;

        return $this;
    }

    public function getProviderAccessToken(): ?string
    {
        return $this->providerAccessToken;
    }

    public function setProviderAccessToken(?string $providerAccessToken): self
    {
        $this->providerAccessToken = $providerAccessToken;

        return $this;
    }

    public function getTokenExpireTime(): ?\DateTimeInterface
    {
        return $this->tokenExpireTime;
    }

    public function setTokenExpireTime(?\DateTimeInterface $tokenExpireTime): self
    {
        $this->tokenExpireTime = $tokenExpireTime;

        return $this;
    }

    public function getTicketExpireTime(): ?\DateTimeInterface
    {
        return $this->ticketExpireTime;
    }

    public function setTicketExpireTime(?\DateTimeInterface $ticketExpireTime): self
    {
        $this->ticketExpireTime = $ticketExpireTime;

        return $this;
    }

    public function getCorpId(): ?string
    {
        return $this->corpId;
    }

    public function setCorpId(?string $corpId): void
    {
        $this->corpId = $corpId;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getEncodingAesKey(): ?string
    {
        return $this->encodingAesKey;
    }

    public function setEncodingAesKey(?string $encodingAesKey): static
    {
        $this->encodingAesKey = $encodingAesKey;

        return $this;
    }

    /**
     * @return Collection<int, Suite>
     */
    public function getSuites(): Collection
    {
        return $this->suites;
    }

    public function addSuite(Suite $suite): static
    {
        if (!$this->suites->contains($suite)) {
            $this->suites->add($suite);
            $suite->setProvider($this);
        }

        return $this;
    }

    public function removeSuite(Suite $suite): static
    {
        if ($this->suites->removeElement($suite)) {
            // set the owning side to null (unless already changed)
            if ($suite->getProvider() === $this) {
                $suite->setProvider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProviderServerMessage>
     */
    public function getServerMessages(): Collection
    {
        return $this->serverMessages;
    }

    public function addServerMessage(ProviderServerMessage $serverMessage): static
    {
        if (!$this->serverMessages->contains($serverMessage)) {
            $this->serverMessages->add($serverMessage);
            $serverMessage->setProvider($this);
        }

        return $this;
    }

    public function removeServerMessage(ProviderServerMessage $serverMessage): static
    {
        if ($this->serverMessages->removeElement($serverMessage)) {
            // set the owning side to null (unless already changed)
            if ($serverMessage->getProvider() === $this) {
                $serverMessage->setProvider(null);
            }
        }

        return $this;
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }
}
