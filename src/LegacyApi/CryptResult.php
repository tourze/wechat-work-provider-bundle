<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\LegacyApi;

/**
 * 加密解密操作的结果对象
 * 替代魔术引用，提供清晰的返回值结构
 */
final readonly class CryptResult
{
    public function __construct(
        public int $errorCode,
        public string $data = '',
    ) {
    }

    /**
     * 创建成功结果
     */
    public static function success(string $data = ''): self
    {
        return new self(ErrorCode::$OK, $data);
    }

    /**
     * 创建错误结果
     */
    public static function error(int $errorCode): self
    {
        return new self($errorCode);
    }

    /**
     * 判断操作是否成功
     */
    public function isSuccess(): bool
    {
        return ErrorCode::$OK === $this->errorCode;
    }

    /**
     * 判断操作是否失败
     */
    public function isError(): bool
    {
        return !$this->isSuccess();
    }
}
