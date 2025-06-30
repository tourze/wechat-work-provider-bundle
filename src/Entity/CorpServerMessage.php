<?php

namespace WechatWorkProviderBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use WechatWorkProviderBundle\Repository\CorpServerMessageRepository;

#[ORM\Entity(repositoryClass: CorpServerMessageRepository::class)]
#[ORM\Table(name: 'wechat_work_provider_corp_server_message', options: ['comment' => '代开发回调'])]
class CorpServerMessage implements \Stringable
{
    use SnowflakeKeyAware;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '企业微信CorpID'])]
    private ?string $toUserName = null;

    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '成员UserID'])]
    private ?string $fromUserName = null;

    #[ORM\ManyToOne(inversedBy: 'serverMessages')]
    private ?AuthCorp $authCorp = null;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '消息创建时间戳'])]
    private ?int $createTime = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => 'Encrypt参数解密后的内容'])]
    private array $decryptData = [];

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '原始数据'])]
    private ?array $rawData = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '消息类型'])]
    private ?string $msgType = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '事件类型'])]
    private ?string $event = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '变更类型'])]
    private ?string $changeType = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '群聊ID'])]
    private ?string $chatId = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '外部联系人ID'])]
    private ?string $externalUserId = null;

    #[ORM\Column(nullable: true, options: ['comment' => '加入场景'])]
    private ?int $joinScene = null;

    #[ORM\Column(nullable: true, options: ['comment' => '成员变更数量'])]
    private ?int $memChangeCnt = null;

    #[ORM\Column(nullable: true, options: ['comment' => '退出场景'])]
    private ?int $quitScene = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '状态'])]
    private ?string $state = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '更新详情'])]
    private ?string $updateDetail = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '用户ID'])]
    private ?string $userId = null;

    #[ORM\Column(length: 140, nullable: true, options: ['comment' => '欢迎语Code'])]
    private ?string $welcomeCode = null;

    #[ORM\Column(nullable: true, options: ['comment' => '响应数据'])]
    private ?array $response = null;


    public function getToUserName(): ?string
    {
        return $this->toUserName;
    }

    public function setToUserName(string $toUserName): self
    {
        $this->toUserName = $toUserName;

        return $this;
    }

    public function getCreateTime(): ?int
    {
        return $this->createTime;
    }

    public function setCreateTime(int $createTime): self
    {
        $this->createTime = $createTime;

        return $this;
    }

    public function getRawData(): ?array
    {
        return $this->rawData;
    }

    public function setRawData(?array $rawData): self
    {
        $this->rawData = $rawData;

        return $this;
    }

    public function getAuthCorp(): ?AuthCorp
    {
        return $this->authCorp;
    }

    public function setAuthCorp(?AuthCorp $authCorp): static
    {
        $this->authCorp = $authCorp;

        return $this;
    }

    public function getFromUserName(): ?string
    {
        return $this->fromUserName;
    }

    public function setFromUserName(?string $fromUserName): self
    {
        $this->fromUserName = $fromUserName;

        return $this;
    }

    public function getDecryptData(): ?array
    {
        return $this->decryptData;
    }

    public function setDecryptData(?array $decryptData): self
    {
        $this->decryptData = $decryptData;

        return $this;
    }

    public function setContext(?array $context): self
    {
        $this->decryptData = $context ?? [];

        return $this;
    }

    public function getMsgType(): ?string
    {
        return $this->msgType;
    }

    public function setMsgType(?string $msgType): self
    {
        $this->msgType = $msgType;

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(?string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getChangeType(): ?string
    {
        return $this->changeType;
    }

    public function setChangeType(?string $changeType): self
    {
        $this->changeType = $changeType;

        return $this;
    }

    public function getChatId(): ?string
    {
        return $this->chatId;
    }

    public function setChatId(?string $chatId): self
    {
        $this->chatId = $chatId;

        return $this;
    }

    public function getExternalUserId(): ?string
    {
        return $this->externalUserId;
    }

    public function setExternalUserId(?string $externalUserId): self
    {
        $this->externalUserId = $externalUserId;

        return $this;
    }

    public function getJoinScene(): ?int
    {
        return $this->joinScene;
    }

    public function setJoinScene(?int $joinScene): self
    {
        $this->joinScene = $joinScene;

        return $this;
    }

    public function getMemChangeCnt(): ?int
    {
        return $this->memChangeCnt;
    }

    public function setMemChangeCnt(?int $memChangeCnt): self
    {
        $this->memChangeCnt = $memChangeCnt;

        return $this;
    }

    public function getQuitScene(): ?int
    {
        return $this->quitScene;
    }

    public function setQuitScene(?int $quitScene): self
    {
        $this->quitScene = $quitScene;

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

    public function getUpdateDetail(): ?string
    {
        return $this->updateDetail;
    }

    public function setUpdateDetail(?string $updateDetail): self
    {
        $this->updateDetail = $updateDetail;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getWelcomeCode(): ?string
    {
        return $this->welcomeCode;
    }

    public function setWelcomeCode(?string $welcomeCode): self
    {
        $this->welcomeCode = $welcomeCode;

        return $this;
    }

    public function getResponse(): ?array
    {
        return $this->response;
    }

    public function setResponse(?array $response): static
    {
        $this->response = $response;

        return $this;
    }


    public function __toString(): string
    {
        return sprintf('%s User:%s', 'CorpServerMessage', $this->userId ?? 'unknown');
    }
}
