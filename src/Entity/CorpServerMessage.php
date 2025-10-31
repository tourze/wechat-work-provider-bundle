<?php

namespace WechatWorkProviderBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use WechatWorkProviderBundle\Repository\CorpServerMessageRepository;

#[ORM\Entity(repositoryClass: CorpServerMessageRepository::class)]
#[ORM\Table(name: 'wechat_work_provider_corp_server_message', options: ['comment' => '代开发回调'])]
class CorpServerMessage implements \Stringable
{
    use SnowflakeKeyAware;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '企业微信CorpID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private ?string $toUserName = null;

    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '成员UserID'])]
    #[Assert\Length(max: 128)]
    private ?string $fromUserName = null;

    #[ORM\ManyToOne(targetEntity: AuthCorp::class, inversedBy: 'serverMessages')]
    private ?AuthCorp $authCorp = null;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '消息创建时间戳'])]
    #[Assert\PositiveOrZero]
    private ?int $createTime = null;

    /**
     * @var array<mixed>
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => 'Encrypt参数解密后的内容'])]
    #[Assert\Type(type: 'array')]
    private array $decryptData = [];

    /**
     * @var array<mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '原始数据'])]
    #[Assert\Type(type: 'array')]
    private ?array $rawData = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '消息类型'])]
    #[Assert\Length(max: 50)]
    private ?string $msgType = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '事件类型'])]
    #[Assert\Length(max: 120)]
    private ?string $event = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '变更类型'])]
    #[Assert\Length(max: 120)]
    private ?string $changeType = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '群聊ID'])]
    #[Assert\Length(max: 120)]
    private ?string $chatId = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '外部联系人ID'])]
    #[Assert\Length(max: 120)]
    private ?string $externalUserId = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '加入场景'])]
    #[Assert\PositiveOrZero]
    private ?int $joinScene = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '成员变更数量'])]
    #[Assert\PositiveOrZero]
    private ?int $memChangeCnt = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '退出场景'])]
    #[Assert\PositiveOrZero]
    private ?int $quitScene = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '状态'])]
    #[Assert\Length(max: 120)]
    private ?string $state = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '更新详情'])]
    #[Assert\Length(max: 120)]
    private ?string $updateDetail = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '用户ID'])]
    #[Assert\Length(max: 120)]
    private ?string $userId = null;

    #[ORM\Column(length: 140, nullable: true, options: ['comment' => '欢迎语Code'])]
    #[Assert\Length(max: 140)]
    private ?string $welcomeCode = null;

    /**
     * @var array<mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '响应数据'])]
    #[Assert\Type(type: 'array')]
    private ?array $response = null;

    public function getToUserName(): ?string
    {
        return $this->toUserName;
    }

    public function setToUserName(string $toUserName): void
    {
        $this->toUserName = $toUserName;
    }

    public function getCreateTime(): ?int
    {
        return $this->createTime;
    }

    public function setCreateTime(int $createTime): void
    {
        $this->createTime = $createTime;
    }

    /**
     * @return array<mixed>|null
     */
    public function getRawData(): ?array
    {
        return $this->rawData;
    }

    /**
     * @param array<mixed>|null $rawData
     */
    public function setRawData(?array $rawData): void
    {
        $this->rawData = $rawData;
    }

    public function getAuthCorp(): ?AuthCorp
    {
        return $this->authCorp;
    }

    public function setAuthCorp(?AuthCorp $authCorp): void
    {
        $this->authCorp = $authCorp;
    }

    public function getFromUserName(): ?string
    {
        return $this->fromUserName;
    }

    public function setFromUserName(?string $fromUserName): void
    {
        $this->fromUserName = $fromUserName;
    }

    /**
     * @return array<mixed>|null
     */
    public function getDecryptData(): ?array
    {
        return $this->decryptData;
    }

    /**
     * @param array<mixed>|null $decryptData
     */
    public function setDecryptData(?array $decryptData): void
    {
        $this->decryptData = $decryptData ?? [];
    }

    /**
     * @param array<mixed>|null $context
     */
    public function setContext(?array $context): void
    {
        $this->decryptData = $context ?? [];
    }

    public function getMsgType(): ?string
    {
        return $this->msgType;
    }

    public function setMsgType(?string $msgType): void
    {
        $this->msgType = $msgType;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(?string $event): void
    {
        $this->event = $event;
    }

    public function getChangeType(): ?string
    {
        return $this->changeType;
    }

    public function setChangeType(?string $changeType): void
    {
        $this->changeType = $changeType;
    }

    public function getChatId(): ?string
    {
        return $this->chatId;
    }

    public function setChatId(?string $chatId): void
    {
        $this->chatId = $chatId;
    }

    public function getExternalUserId(): ?string
    {
        return $this->externalUserId;
    }

    public function setExternalUserId(?string $externalUserId): void
    {
        $this->externalUserId = $externalUserId;
    }

    public function getJoinScene(): ?int
    {
        return $this->joinScene;
    }

    public function setJoinScene(?int $joinScene): void
    {
        $this->joinScene = $joinScene;
    }

    public function getMemChangeCnt(): ?int
    {
        return $this->memChangeCnt;
    }

    public function setMemChangeCnt(?int $memChangeCnt): void
    {
        $this->memChangeCnt = $memChangeCnt;
    }

    public function getQuitScene(): ?int
    {
        return $this->quitScene;
    }

    public function setQuitScene(?int $quitScene): void
    {
        $this->quitScene = $quitScene;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getUpdateDetail(): ?string
    {
        return $this->updateDetail;
    }

    public function setUpdateDetail(?string $updateDetail): void
    {
        $this->updateDetail = $updateDetail;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): void
    {
        $this->userId = $userId;
    }

    public function getWelcomeCode(): ?string
    {
        return $this->welcomeCode;
    }

    public function setWelcomeCode(?string $welcomeCode): void
    {
        $this->welcomeCode = $welcomeCode;
    }

    /**
     * @return array<mixed>|null
     */
    public function getResponse(): ?array
    {
        return $this->response;
    }

    /**
     * @param array<mixed>|null $response
     */
    public function setResponse(?array $response): void
    {
        $this->response = $response;
    }

    public function __toString(): string
    {
        return sprintf('%s User:%s', 'CorpServerMessage', $this->userId ?? 'unknown');
    }
}
