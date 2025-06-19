<?php

namespace WechatWorkProviderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatWorkProviderBundle\Repository\SuiteRepository;

#[ORM\Entity(repositoryClass: SuiteRepository::class)]
#[ORM\Table(name: 'wechat_work_provider_suite', options: ['comment' => '代开发应用模板'])]
class Suite implements \Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'suites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Provider $provider = null;

    /**
     * @var string|null 第三方应用id或者代开发应用模板id。第三方应用以ww或wx开头应用id（对应于旧的以tj开头的套件id）；代开发应用以dk开头
     */
    #[ORM\Column(length: 64, options: ['comment' => '模板ID'])]
    private ?string $suiteId = null;

    #[ORM\Column(length: 200, options: ['comment' => '模板Secret'])]
    private ?string $suiteSecret = null;

    #[ORM\Column(length: 250, nullable: true, options: ['comment' => '模板Ticket'])]
    private ?string $suiteTicket = null;

    #[ORM\Column(length: 200, nullable: true, options: ['comment' => 'AccessToken'])]
    private ?string $suiteAccessToken = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'Token过期时间'])]
    private ?\DateTimeImmutable $tokenExpireTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'Ticket过期时间'])]
    private ?\DateTimeImmutable $ticketExpireTime = null;

    #[ORM\OneToMany(mappedBy: 'suite', targetEntity: AuthCorp::class)]
    private Collection $authCorps;

    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '回调用Token'])]
    private ?string $token = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '回调用EncodingAESKey'])]
    private ?string $encodingAesKey = null;

    #[ORM\OneToMany(mappedBy: 'suite', targetEntity: SuiteServerMessage::class)]
    private Collection $serverMessages;

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
    }}
