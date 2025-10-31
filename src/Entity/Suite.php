<?php

namespace WechatWorkProviderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatWorkProviderBundle\Repository\SuiteRepository;

#[ORM\Entity(repositoryClass: SuiteRepository::class)]
#[ORM\Table(name: 'wechat_work_provider_suite', options: ['comment' => '代开发应用模板'])]
class Suite implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'suites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Provider $provider = null;

    /**
     * @var string|null 第三方应用id或者代开发应用模板id。第三方应用以ww或wx开头应用id（对应于旧的以tj开头的套件id）；代开发应用以dk开头
     */
    #[ORM\Column(length: 64, options: ['comment' => '模板ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private ?string $suiteId = null;

    #[ORM\Column(length: 200, options: ['comment' => '模板Secret'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    private ?string $suiteSecret = null;

    #[ORM\Column(length: 250, nullable: true, options: ['comment' => '模板Ticket'])]
    #[Assert\Length(max: 250)]
    private ?string $suiteTicket = null;

    #[ORM\Column(length: 200, nullable: true, options: ['comment' => 'AccessToken'])]
    #[Assert\Length(max: 200)]
    private ?string $suiteAccessToken = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'Token过期时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $tokenExpireTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'Ticket过期时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $ticketExpireTime = null;

    /**
     * @var Collection<int, AuthCorp>
     */
    #[ORM\OneToMany(targetEntity: AuthCorp::class, mappedBy: 'suite')]
    private Collection $authCorps;

    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '回调用Token'])]
    #[Assert\Length(max: 40)]
    private ?string $token = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '回调用EncodingAESKey'])]
    #[Assert\Length(max: 120)]
    private ?string $encodingAesKey = null;

    /**
     * @var Collection<int, SuiteServerMessage>
     */
    #[ORM\OneToMany(targetEntity: SuiteServerMessage::class, mappedBy: 'suite')]
    private Collection $serverMessages;

    public function __construct()
    {
        $this->authCorps = new ArrayCollection();
        $this->serverMessages = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (null === $this->getId() || '' === $this->getId()) {
            return '';
        }

        return "{$this->getSuiteId()}";
    }

    public function getSuiteSecret(): ?string
    {
        return $this->suiteSecret;
    }

    public function setSuiteSecret(string $suiteSecret): void
    {
        $this->suiteSecret = $suiteSecret;
    }

    public function getSuiteTicket(): ?string
    {
        return $this->suiteTicket;
    }

    public function setSuiteTicket(?string $suiteTicket): void
    {
        $this->suiteTicket = $suiteTicket;
    }

    public function getSuiteAccessToken(): ?string
    {
        return $this->suiteAccessToken;
    }

    public function setSuiteAccessToken(?string $suiteAccessToken): void
    {
        $this->suiteAccessToken = $suiteAccessToken;
    }

    public function getTokenExpireTime(): ?\DateTimeImmutable
    {
        return $this->tokenExpireTime;
    }

    public function setTokenExpireTime(?\DateTimeImmutable $tokenExpireTime): void
    {
        $this->tokenExpireTime = $tokenExpireTime;
    }

    public function getTicketExpireTime(): ?\DateTimeImmutable
    {
        return $this->ticketExpireTime;
    }

    public function setTicketExpireTime(?\DateTimeImmutable $ticketExpireTime): void
    {
        $this->ticketExpireTime = $ticketExpireTime;
    }

    public function getSuiteId(): ?string
    {
        return $this->suiteId;
    }

    public function setSuiteId(string $suiteId): void
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

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getEncodingAesKey(): ?string
    {
        return $this->encodingAesKey;
    }

    public function setEncodingAesKey(?string $encodingAesKey): void
    {
        $this->encodingAesKey = $encodingAesKey;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): void
    {
        $this->provider = $provider;
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
}
