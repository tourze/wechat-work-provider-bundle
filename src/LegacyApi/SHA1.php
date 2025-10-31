<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\LegacyApi;

/**
 * SHA1 class
 *
 * 计算公众平台的消息签名接口.
 */
class SHA1
{
    /**
     * 用SHA1算法生成安全签名
     *
     * @param string $token     票据
     * @param string $timestamp 时间戳
     * @param string $nonce     随机字符串
     * @param string $encrypt_msg
     * @return array{0: int, 1: string|null}
     */
    public function getSHA1(string $token, string $timestamp, string $nonce, string $encrypt_msg): array
    {
        // 排序
        try {
            $array = [$encrypt_msg, $token, $timestamp, $nonce];
            sort($array, SORT_STRING);
            $str = implode($array);

            return [ErrorCode::$OK, sha1($str)];
        } catch (\Throwable $e) {
            echo $e . "\n";

            return [ErrorCode::$ComputeSignatureError, null];
        }
    }
}
