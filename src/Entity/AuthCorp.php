<?php

namespace WechatWorkProviderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatWorkBundle\Entity\AccessTokenAware;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;

/**
 * 授权企业信息
 *
 * @see https://developer.work.weixin.qq.com/document/path/90603
 */
#[ORM\Entity(repositoryClass: AuthCorpRepository::class)]
#[ORM\Table(name: 'wechat_work_provider_auth_corp', options: ['comment' => '授权方公司'])]
class AuthCorp implements AccessTokenAware, \Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\Column(length: 80, options: ['comment' => '授权方企业微信id'])]
    private ?string $corpId = null;

    #[ORM\Column(length: 120, options: ['comment' => '授权方企业简称'])]
    private ?string $corpName = null;

    #[ORM\Column(length: 30, nullable: true, options: ['comment' => '授权方企业类型'])]
    private ?string $corpType = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '授权方企业方形头像'])]
    private ?string $corpSquareLogoUrl = null;

    #[ORM\Column(nullable: true, options: ['comment' => '授权方企业用户规模'])]
    private ?int $corpUserMax = null;

    #[ORM\Column(length: 200, nullable: true, options: ['comment' => '授权方企业全称'])]
    private ?string $corpFullName = null;

    #[ORM\Column(length: 16, nullable: true, options: ['comment' => '企业类型'])]
    private ?string $subjectType = null;

    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '企业规模'])]
    private ?string $corpScale = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '企业所属行业'])]
    private ?string $corpIndustry = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '企业所属子行业'])]
    private ?string $corpSubIndustry = null;

    #[ORM\Column(nullable: true, options: ['comment' => '授权信息'])]
    private array $authInfo = [];

    #[ORM\Column(nullable: true, options: ['comment' => '授权管理员的信息'])]
    private array $authUserInfo = [];

    #[ORM\Column(nullable: true, options: ['comment' => '代理服务商企业信息'])]
    private ?array $dealerCorpInfo = [];

    #[ORM\Column(nullable: true, options: ['comment' => '推广二维码安装相关信息'])]
    private ?array $registerCodeInfo = [];

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '安装应用时，扫码或者授权链接中带的state值'])]
    private ?string $state = null;

    #[ORM\Column(length: 200, nullable: true, options: ['comment' => '企业微信永久授权码'])]
    private ?string $permanentCode = null;

    #[ORM\Column(length: 300, nullable: true, options: ['comment' => '授权方（企业）access_token'])]
    private ?string $accessToken = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '授权方（企业）access_token超时时间'])]
    private ?\DateTimeImmutable $tokenExpireTime = null;

    #[ORM\ManyToOne(inversedBy: 'authCorps')]
    private ?Suite $suite = null;

    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '代开发Token'])]
    private ?string $token = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '代开发EncodingAESKey'])]
    private ?string $encodingAesKey = null;

    #[ORM\OneToMany(mappedBy: 'authCorp', targetEntity: CorpServerMessage::class)]
    private Collection $serverMessages;

    public function __construct()
    {
        $this->serverMessages = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getCorpName()}";
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCorpId(): ?string
    {
        return $this->corpId;
    }

    public function setCorpId(string $corpId): self
    {
        $this->corpId = $corpId;

        return $this;
    }

    public function getCorpName(): ?string
    {
        return $this->corpName;
    }

    public function setCorpName(string $corpName): self
    {
        $this->corpName = $corpName;

        return $this;
    }

    public function getCorpType(): ?string
    {
        return $this->corpType;
    }

    public function setCorpType(?string $corpType): self
    {
        $this->corpType = $corpType;

        return $this;
    }

    public function getCorpSquareLogoUrl(): ?string
    {
        return $this->corpSquareLogoUrl;
    }

    public function setCorpSquareLogoUrl(?string $corpSquareLogoUrl): self
    {
        $this->corpSquareLogoUrl = $corpSquareLogoUrl;

        return $this;
    }

    public function getCorpUserMax(): ?int
    {
        return $this->corpUserMax;
    }

    public function setCorpUserMax(?int $corpUserMax): self
    {
        $this->corpUserMax = $corpUserMax;

        return $this;
    }

    public function getCorpFullName(): ?string
    {
        return $this->corpFullName;
    }

    public function setCorpFullName(?string $corpFullName): self
    {
        $this->corpFullName = $corpFullName;

        return $this;
    }

    public function getSubjectType(): ?string
    {
        return $this->subjectType;
    }

    public function setSubjectType(?string $subjectType): self
    {
        $this->subjectType = $subjectType;

        return $this;
    }

    public function getCorpScale(): ?string
    {
        return $this->corpScale;
    }

    public function setCorpScale(?string $corpScale): self
    {
        $this->corpScale = $corpScale;

        return $this;
    }

    public function getCorpIndustry(): ?string
    {
        return $this->corpIndustry;
    }

    public function setCorpIndustry(?string $corpIndustry): self
    {
        $this->corpIndustry = $corpIndustry;

        return $this;
    }

    public function getCorpSubIndustry(): ?string
    {
        return $this->corpSubIndustry;
    }

    public function setCorpSubIndustry(?string $corpSubIndustry): self
    {
        $this->corpSubIndustry = $corpSubIndustry;

        return $this;
    }

    public function getAuthInfo(): array
    {
        return $this->authInfo;
    }

    public function setAuthInfo(?array $authInfo): self
    {
        $this->authInfo = $authInfo ?? [];

        return $this;
    }

    public function getAuthUserInfo(): array
    {
        return $this->authUserInfo;
    }

    public function setAuthUserInfo(?array $authUserInfo): self
    {
        $this->authUserInfo = $authUserInfo ?? [];

        return $this;
    }

    public function getDealerCorpInfo(): array
    {
        return $this->dealerCorpInfo;
    }

    public function setDealerCorpInfo(?array $dealerCorpInfo): self
    {
        $this->dealerCorpInfo = $dealerCorpInfo ?? [];

        return $this;
    }

    public function getRegisterCodeInfo(): array
    {
        return $this->registerCodeInfo;
    }

    public function setRegisterCodeInfo(?array $registerCodeInfo): self
    {
        $this->registerCodeInfo = $registerCodeInfo ?? [];

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getPermanentCode(): ?string
    {
        return $this->permanentCode;
    }

    public function setPermanentCode(?string $permanentCode): self
    {
        $this->permanentCode = $permanentCode;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): self
    {
        $this->accessToken = $accessToken;

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

    public function getSuite(): ?Suite
    {
        return $this->suite;
    }

    public function setSuite(?Suite $suite): self
    {
        $this->suite = $suite;

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

    /**
     * @return Collection<int, CorpServerMessage>
     */
    public function getServerMessages(): Collection
    {
        return $this->serverMessages;
    }

    public function addServerMessage(CorpServerMessage $serverMessage): static
    {
        if (!$this->serverMessages->contains($serverMessage)) {
            $this->serverMessages->add($serverMessage);
            $serverMessage->setAuthCorp($this);
        }

        return $this;
    }

    public function removeServerMessage(CorpServerMessage $serverMessage): static
    {
        if ($this->serverMessages->removeElement($serverMessage)) {
            // set the owning side to null (unless already changed)
            if ($serverMessage->getAuthCorp() === $this) {
                $serverMessage->setAuthCorp(null);
            }
        }

        return $this;
    }}
