<?php

namespace WechatWorkProviderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatWorkProviderBundle\Repository\ProviderRepository;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\Table(name: 'wechat_work_provider', options: ['comment' => '服务商'])]
class Provider implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[ORM\Column(length: 64, options: ['comment' => '服务商corpId'])]
    private ?string $corpId = null;

    /**
     * @var string|null 服务商的secret，在服务商管理后台可见
     */
    #[ORM\Column(length: 200, options: ['comment' => '服务商secret'])]
    private ?string $providerSecret = null;

    #[ORM\Column(length: 200, nullable: true, options: ['comment' => '服务商AccessToken'])]
    private ?string $providerAccessToken = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'AccessToken过期时间'])]
    private ?\DateTimeImmutable $tokenExpireTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'Ticket过期时间'])]
    private ?\DateTimeImmutable $ticketExpireTime = null;

    #[ORM\Column(length: 40, nullable: true, options: ['comment' => 'Token'])]
    private ?string $token = null;

    #[ORM\Column(length: 128, nullable: true, options: ['comment' => 'EncodingAESKey'])]
    private ?string $encodingAesKey = null;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Suite::class)]
    private Collection $suites;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: ProviderServerMessage::class)]
    private Collection $serverMessages;

    public function __construct()
    {
        $this->suites = new ArrayCollection();
        $this->serverMessages = new ArrayCollection();
    }

    public function __toString(): string
    {
        if ($this->getCorpId() === null || $this->getCorpId() === '') {
            return '';
        }

        return "{$this->getCorpId()}";
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

    public function getTokenExpireTime(): ?\DateTimeImmutable
    {
        return $this->tokenExpireTime;
    }

    public function setTokenExpireTime(?\DateTimeImmutable $tokenExpireTime): self
    {
        $this->tokenExpireTime = $tokenExpireTime;

        return $this;
    }

    public function getTicketExpireTime(): ?\DateTimeImmutable
    {
        return $this->ticketExpireTime;
    }

    public function setTicketExpireTime(?\DateTimeImmutable $ticketExpireTime): self
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
    }}
