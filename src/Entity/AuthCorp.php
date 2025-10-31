<?php

namespace WechatWorkProviderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
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
    use SnowflakeKeyAware;

    #[ORM\Column(length: 80, options: ['comment' => '授权方企业微信id'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    private ?string $corpId = null;

    #[ORM\Column(length: 120, options: ['comment' => '授权方企业简称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    private ?string $corpName = null;

    #[ORM\Column(length: 30, nullable: true, options: ['comment' => '授权方企业类型'])]
    #[Assert\Length(max: 30)]
    private ?string $corpType = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '授权方企业方形头像'])]
    #[Assert\Length(max: 255)]
    #[Assert\Url]
    private ?string $corpSquareLogoUrl = null;

    #[ORM\Column(nullable: true, options: ['comment' => '授权方企业用户规模'])]
    #[Assert\PositiveOrZero]
    private ?int $corpUserMax = null;

    #[ORM\Column(length: 200, nullable: true, options: ['comment' => '授权方企业全称'])]
    #[Assert\Length(max: 200)]
    private ?string $corpFullName = null;

    #[ORM\Column(length: 16, nullable: true, options: ['comment' => '企业类型'])]
    #[Assert\Length(max: 16)]
    private ?string $subjectType = null;

    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '企业规模'])]
    #[Assert\Length(max: 40)]
    private ?string $corpScale = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '企业所属行业'])]
    #[Assert\Length(max: 100)]
    private ?string $corpIndustry = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '企业所属子行业'])]
    #[Assert\Length(max: 100)]
    private ?string $corpSubIndustry = null;

    /**
     * @var array<mixed>
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '授权信息'])]
    #[Assert\Type(type: 'array')]
    private array $authInfo = [];

    /**
     * @var array<mixed>
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '授权管理员的信息'])]
    #[Assert\Type(type: 'array')]
    private array $authUserInfo = [];

    /**
     * @var array<mixed>
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '代理服务商企业信息'])]
    #[Assert\Type(type: 'array')]
    private array $dealerCorpInfo = [];

    /**
     * @var array<mixed>
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '推广二维码安装相关信息'])]
    #[Assert\Type(type: 'array')]
    private array $registerCodeInfo = [];

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '安装应用时，扫码或者授权链接中带的state值'])]
    #[Assert\Length(max: 100)]
    private ?string $state = null;

    #[ORM\Column(length: 200, nullable: true, options: ['comment' => '企业微信永久授权码'])]
    #[Assert\Length(max: 200)]
    private ?string $permanentCode = null;

    #[ORM\Column(length: 300, nullable: true, options: ['comment' => '授权方（企业）access_token'])]
    #[Assert\Length(max: 300)]
    private ?string $accessToken = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '授权方（企业）access_token超时时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $tokenExpireTime = null;

    #[ORM\ManyToOne(inversedBy: 'authCorps')]
    private ?Suite $suite = null;

    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '代开发Token'])]
    #[Assert\Length(max: 40)]
    private ?string $token = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '代开发EncodingAESKey'])]
    #[Assert\Length(max: 120)]
    private ?string $encodingAesKey = null;

    /**
     * @var Collection<int, CorpServerMessage>
     */
    #[ORM\OneToMany(targetEntity: CorpServerMessage::class, mappedBy: 'authCorp')]
    private Collection $serverMessages;

    public function __construct()
    {
        $this->serverMessages = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (null === $this->getId() || '' === $this->getId()) {
            return '';
        }

        return "{$this->getCorpName()}";
    }

    public function getCorpId(): ?string
    {
        return $this->corpId;
    }

    public function setCorpId(string $corpId): void
    {
        $this->corpId = $corpId;
    }

    public function getCorpName(): ?string
    {
        return $this->corpName;
    }

    public function setCorpName(string $corpName): void
    {
        $this->corpName = $corpName;
    }

    public function getCorpType(): ?string
    {
        return $this->corpType;
    }

    public function setCorpType(?string $corpType): void
    {
        $this->corpType = $corpType;
    }

    public function getCorpSquareLogoUrl(): ?string
    {
        return $this->corpSquareLogoUrl;
    }

    public function setCorpSquareLogoUrl(?string $corpSquareLogoUrl): void
    {
        $this->corpSquareLogoUrl = $corpSquareLogoUrl;
    }

    public function getCorpUserMax(): ?int
    {
        return $this->corpUserMax;
    }

    public function setCorpUserMax(?int $corpUserMax): void
    {
        $this->corpUserMax = $corpUserMax;
    }

    public function getCorpFullName(): ?string
    {
        return $this->corpFullName;
    }

    public function setCorpFullName(?string $corpFullName): void
    {
        $this->corpFullName = $corpFullName;
    }

    public function getSubjectType(): ?string
    {
        return $this->subjectType;
    }

    public function setSubjectType(?string $subjectType): void
    {
        $this->subjectType = $subjectType;
    }

    public function getCorpScale(): ?string
    {
        return $this->corpScale;
    }

    public function setCorpScale(?string $corpScale): void
    {
        $this->corpScale = $corpScale;
    }

    public function getCorpIndustry(): ?string
    {
        return $this->corpIndustry;
    }

    public function setCorpIndustry(?string $corpIndustry): void
    {
        $this->corpIndustry = $corpIndustry;
    }

    public function getCorpSubIndustry(): ?string
    {
        return $this->corpSubIndustry;
    }

    public function setCorpSubIndustry(?string $corpSubIndustry): void
    {
        $this->corpSubIndustry = $corpSubIndustry;
    }

    /**
     * @return array<mixed>
     */
    public function getAuthInfo(): array
    {
        return $this->authInfo;
    }

    /**
     * @param array<mixed>|null $authInfo
     */
    public function setAuthInfo(?array $authInfo): void
    {
        $this->authInfo = $authInfo ?? [];
    }

    /**
     * @return array<mixed>
     */
    public function getAuthUserInfo(): array
    {
        return $this->authUserInfo;
    }

    /**
     * @param array<mixed>|null $authUserInfo
     */
    public function setAuthUserInfo(?array $authUserInfo): void
    {
        $this->authUserInfo = $authUserInfo ?? [];
    }

    /**
     * @return array<mixed>
     */
    public function getDealerCorpInfo(): array
    {
        return $this->dealerCorpInfo;
    }

    /**
     * @param array<mixed>|null $dealerCorpInfo
     */
    public function setDealerCorpInfo(?array $dealerCorpInfo): void
    {
        $this->dealerCorpInfo = $dealerCorpInfo ?? [];
    }

    /**
     * @return array<mixed>
     */
    public function getRegisterCodeInfo(): array
    {
        return $this->registerCodeInfo;
    }

    /**
     * @param array<mixed>|null $registerCodeInfo
     */
    public function setRegisterCodeInfo(?array $registerCodeInfo): void
    {
        $this->registerCodeInfo = $registerCodeInfo ?? [];
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getPermanentCode(): ?string
    {
        return $this->permanentCode;
    }

    public function setPermanentCode(?string $permanentCode): void
    {
        $this->permanentCode = $permanentCode;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getTokenExpireTime(): ?\DateTimeImmutable
    {
        return $this->tokenExpireTime;
    }

    public function setTokenExpireTime(?\DateTimeImmutable $tokenExpireTime): void
    {
        $this->tokenExpireTime = $tokenExpireTime;
    }

    public function getSuite(): ?Suite
    {
        return $this->suite;
    }

    public function setSuite(?Suite $suite): void
    {
        $this->suite = $suite;
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
    }
}
