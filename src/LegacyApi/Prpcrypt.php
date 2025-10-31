<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\LegacyApi;

/**
 * 加解密类
 *
 * 提供接收和推送给公众平台消息的加解密接口.
 */
class Prpcrypt
{
    private string $key;

    private string $iv;

    /**
     * Prpcrypt 构造函数
     * @param string $k
     */
    public function __construct(string $k)
    {
        $decoded = base64_decode($k . '=', true);
        $this->key = false !== $decoded ? $decoded : '';
        $this->iv = substr($this->key, 0, 16);
    }

    /**
     * 加密
     *
     * @param string $text
     * @param string $receiveId
     * @return array{0: int, 1: string|null}
     */
    public function encrypt(string $text, string $receiveId): array
    {
        try {
            // 拼接
            $text = $this->getRandomStr() . pack('N', strlen($text)) . $text . $receiveId;
            // 添加PKCS#7填充
            $pkc_encoder = new PKCS7Encoder();
            $text = $pkc_encoder->encode($text);
            // 加密
            $encrypted = openssl_encrypt($text, 'AES-256-CBC', $this->key, OPENSSL_ZERO_PADDING, $this->iv);

            return [ErrorCode::$OK, false === $encrypted ? null : $encrypted];
        } catch (\Throwable $e) {
            echo $e;

            return [ErrorCode::$EncryptAESError, null];
        }
    }

    /**
     * 解密
     *
     * @param string $encrypted
     * @param string $receiveId
     * @return array{0: int, 1: string|null}
     */
    public function decrypt(string $encrypted, string $receiveId): array
    {
        try {
            // 解密
            $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $this->key, OPENSSL_ZERO_PADDING, $this->iv);
            if (false === $decrypted) {
                return [ErrorCode::$DecryptAESError, null];
            }
        } catch (\Throwable $e) {
            return [ErrorCode::$DecryptAESError, null];
        }
        try {
            // 删除PKCS#7填充
            $pkc_encoder = new PKCS7Encoder();
            $result = $pkc_encoder->decode($decrypted);
            if (strlen($result) < 16) {
                return [ErrorCode::$IllegalBuffer, null];
            }
            // 拆分
            $content = substr($result, 16, strlen($result));
            $len_list = unpack('N', substr($content, 0, 4));
            if (false === $len_list || !isset($len_list[1])) {
                return [ErrorCode::$IllegalBuffer, null];
            }
            $xml_len = is_int($len_list[1]) ? $len_list[1] : 0;
            $xml_content = substr($content, 4, $xml_len);
            $from_receiveId = substr($content, $xml_len + 4);
        } catch (\Throwable $e) {
            echo $e;

            return [ErrorCode::$IllegalBuffer, null];
        }
        if ($from_receiveId !== $receiveId) {
            return [ErrorCode::$ValidateCorpidError, null];
        }

        return [0, $xml_content];
    }

    /**
     * 生成随机字符串
     *
     * @return string
     */
    private function getRandomStr(): string
    {
        $str = '';
        $str_pol = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyl';
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; ++$i) {
            $str .= $str_pol[mt_rand(0, $max)];
        }

        return $str;
    }
}
