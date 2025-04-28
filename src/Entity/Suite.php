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
use WechatWorkProviderBundle\Repository\SuiteRepository;

#[Creatable]
#[Editable]
#[Deletable]
#[ORM\Entity(repositoryClass: SuiteRepository::class)]
#[ORM\Table(name: 'wechat_work_provider_suite', options: ['comment' => '代开发应用模板'])]
class Suite implements \Stringable
{
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ListColumn(title: '服务商')]
    #[FormField(title: '服务商')]
    #[ORM\ManyToOne(inversedBy: 'suites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Provider $provider = null;

    /**
     * @var string|null 第三方应用id或者代开发应用模板id。第三方应用以ww或wx开头应用id（对应于旧的以tj开头的套件id）；代开发应用以dk开头
     */
    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 64, options: ['comment' => '模板ID'])]
    private ?string $suiteId = null;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 200, options: ['comment' => '模板Secret'])]
    private ?string $suiteSecret = null;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 250, nullable: true, options: ['comment' => '模板Ticket'])]
    private ?string $suiteTicket = null;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 200, nullable: true, options: ['comment' => 'AccessToken'])]
    private ?string $suiteAccessToken = null;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => 'Token过期时间'])]
    private ?\DateTimeInterface $tokenExpireTime = null;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => 'Ticket过期时间'])]
    private ?\DateTimeInterface $ticketExpireTime = null;

    #[ORM\OneToMany(mappedBy: 'suite', targetEntity: AuthCorp::class)]
    private Collection $authCorps;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '回调用Token'])]
    private ?string $token = null;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '回调用EncodingAESKey'])]
    private ?string $encodingAesKey = null;

    #[ORM\OneToMany(mappedBy: 'suite', targetEntity: SuiteServerMessage::class)]
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
        $this->authCorps = new ArrayCollection();
        $this->serverMessages = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getSuiteId()}";
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSuiteSecret(): ?string
    {
        return $this->suiteSecret;
    }

    public function setSuiteSecret(string $suiteSecret): self
    {
        $this->suiteSecret = $suiteSecret;

        return $this;
    }

    public function getSuiteTicket(): ?string
    {
        return $this->suiteTicket;
    }

    public function setSuiteTicket(?string $suiteTicket): self
    {
        $this->suiteTicket = $suiteTicket;

        return $this;
    }

    public function getSuiteAccessToken(): ?string
    {
        return $this->suiteAccessToken;
    }

    public function setSuiteAccessToken(?string $suiteAccessToken): self
    {
        $this->suiteAccessToken = $suiteAccessToken;

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

    public function getSuiteId(): ?string
    {
        return $this->suiteId;
    }

    public function setSuiteId(?string $suiteId): void
    {
        $this->suiteId = $suiteId;
    }

    /**
     * @return Collection<int, AuthCorp>
     */
    public function getAuthCorps(): Collection
    {
        return $this->authCorps;
    }

    public function addAuthCorps(AuthCorp $authCorps): self
    {
        if (!$this->authCorps->contains($authCorps)) {
            $this->authCorps->add($authCorps);
            $authCorps->setSuite($this);
        }

        return $this;
    }

    public function removeAuthCorps(AuthCorp $authCorps): self
    {
        if ($this->authCorps->removeElement($authCorps)) {
            // set the owning side to null (unless already changed)
            if ($authCorps->getSuite() === $this) {
                $authCorps->setSuite(null);
            }
        }

        return $this;
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

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return Collection<int, SuiteServerMessage>
     */
    public function getServerMessages(): Collection
    {
        return $this->serverMessages;
    }

    public function addServerMessage(SuiteServerMessage $serverMessage): static
    {
        if (!$this->serverMessages->contains($serverMessage)) {
            $this->serverMessages->add($serverMessage);
            $serverMessage->setSuite($this);
        }

        return $this;
    }

    public function removeServerMessage(SuiteServerMessage $serverMessage): static
    {
        if ($this->serverMessages->removeElement($serverMessage)) {
            // set the owning side to null (unless already changed)
            if ($serverMessage->getSuite() === $this) {
                $serverMessage->setSuite(null);
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
