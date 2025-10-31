<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\LegacyApi;

class WXBizMsgCrypt
{
    private string $m_sToken;

    private string $m_sEncodingAesKey;

    private string $m_sReceiveId;

    /**
     * 构造函数
     *
     * @param $token          string 开发者设置的token
     * @param $encodingAesKey string 开发者设置的EncodingAESKey
     * @param $receiveId      string, 不同应用场景传不同的id
     */
    public function __construct(string $token, string $encodingAesKey, string $receiveId)
    {
        $this->m_sToken = $token;
        $this->m_sEncodingAesKey = $encodingAesKey;
        $this->m_sReceiveId = $receiveId;
    }

    /**
     * 验证URL
     * @param string $sMsgSignature 签名串，对应URL参数的msg_signature
     * @param string $sTimeStamp 时间戳，对应URL参数的timestamp
     * @param string $sNonce 随机串，对应URL参数的nonce
     * @param string $sEchoStr 随机串，对应URL参数的echostr
     * @return CryptResult 成功时data包含解密后的echostr，失败时errorCode包含错误码
     */
    public function VerifyURL(string $sMsgSignature, string $sTimeStamp, string $sNonce, string $sEchoStr): CryptResult
    {
        if (43 !== strlen($this->m_sEncodingAesKey)) {
            return CryptResult::error(ErrorCode::$IllegalAesKey);
        }

        $pc = new Prpcrypt($this->m_sEncodingAesKey);
        // verify msg_signature
        $sha1 = new SHA1();
        $array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $sEchoStr);
        $ret = $array[0];

        if (0 !== $ret) {
            return CryptResult::error($ret);
        }

        $signature = $array[1];
        if ($signature !== $sMsgSignature) {
            return CryptResult::error(ErrorCode::$ValidateSignatureError);
        }

        $result = $pc->decrypt($sEchoStr, $this->m_sReceiveId);
        if (0 !== $result[0]) {
            return CryptResult::error($result[0]);
        }
        $sReplyEchoStr = $result[1] ?? '';

        return CryptResult::success($sReplyEchoStr);
    }

    /**
     * 将公众平台回复用户的消息加密打包.
     * <ol>
     *    <li>对要发送的消息进行AES-CBC加密</li>
     *    <li>生成安全签名</li>
     *    <li>将消息密文和安全签名打包成xml格式</li>
     * </ol>
     *
     * @param string $sReplyMsg
     * @param int|null $sTimeStamp
     * @param string $sNonce
     * @return CryptResult 成功时data包含加密后的XML消息，失败时errorCode包含错误码
     */
    public function EncryptMsg(string $sReplyMsg, ?int $sTimeStamp, string $sNonce): CryptResult
    {
        $pc = new Prpcrypt($this->m_sEncodingAesKey);

        // 加密
        $array = $pc->encrypt($sReplyMsg, $this->m_sReceiveId);
        $ret = $array[0];
        if (0 !== $ret) {
            return CryptResult::error($ret);
        }

        if (null === $sTimeStamp) {
            $sTimeStamp = time();
        }
        $encrypt = $array[1] ?? '';

        // 生成安全签名
        $sha1 = new SHA1();
        $array = $sha1->getSHA1($this->m_sToken, (string) $sTimeStamp, $sNonce, $encrypt);
        $ret = $array[0];
        if (0 !== $ret) {
            return CryptResult::error($ret);
        }
        $signature = $array[1] ?? '';

        // 生成发送的xml
        $xmlparse = new XMLParse();
        $sEncryptMsg = $xmlparse->generate($encrypt, $signature, (string) $sTimeStamp, $sNonce);

        return CryptResult::success($sEncryptMsg);
    }

    /**
     * 检验消息的真实性，并且获取解密后的明文.
     * <ol>
     *    <li>利用收到的密文生成安全签名，进行签名验证</li>
     *    <li>若验证通过，则提取xml中的加密消息</li>
     *    <li>对消息进行解密</li>
     * </ol>
     *
     * @param string $sMsgSignature
     * @param int|null $sTimeStamp
     * @param string $sNonce
     * @param string $sPostData
     * @return CryptResult 成功时data包含解密后的消息，失败时errorCode包含错误码
     */
    public function DecryptMsg(string $sMsgSignature, ?int $sTimeStamp, string $sNonce, string $sPostData): CryptResult
    {
        $aesKeyValidation = $this->validateAesKey();
        if (ErrorCode::$OK !== $aesKeyValidation) {
            return CryptResult::error($aesKeyValidation);
        }

        $pc = new Prpcrypt($this->m_sEncodingAesKey);

        $encryptResult = $this->extractEncryptFromPostData($sPostData);
        if (is_int($encryptResult)) {
            return CryptResult::error($encryptResult);
        }
        $encrypt = $encryptResult;

        $sTimeStamp = $this->normalizeTimestamp($sTimeStamp);

        $signatureValidation = $this->validateSignature($sMsgSignature, $sTimeStamp, $sNonce, $encrypt);
        if (ErrorCode::$OK !== $signatureValidation) {
            return CryptResult::error($signatureValidation);
        }

        $decryptResult = $pc->decrypt($encrypt, $this->m_sReceiveId);
        if (0 !== $decryptResult[0]) {
            return CryptResult::error($decryptResult[0]);
        }

        $sMsg = $decryptResult[1] ?? '';

        return CryptResult::success($sMsg);
    }

    private function validateAesKey(): int
    {
        if (43 !== strlen($this->m_sEncodingAesKey)) {
            return ErrorCode::$IllegalAesKey;
        }

        return ErrorCode::$OK;
    }

    /**
     * @param string $sPostData
     * @return string|int
     */
    private function extractEncryptFromPostData(string $sPostData): string|int
    {
        if (json_validate($sPostData)) {
            $decoded = json_decode($sPostData, true);
            if (is_array($decoded) && isset($decoded['Encrypt']) && is_string($decoded['Encrypt'])) {
                return $decoded['Encrypt'];
            }
        }

        $xmlparse = new XMLParse();
        $array = $xmlparse->extract($sPostData);
        $ret = $array[0];

        if (0 !== $ret) {
            return (int) $ret;
        }

        return $array[1] ?? '';
    }

    /**
     * @param int|null $sTimeStamp
     * @return string
     */
    private function normalizeTimestamp(?int $sTimeStamp): string
    {
        return null === $sTimeStamp ? (string) time() : (string) $sTimeStamp;
    }

    /**
     * @param string $sMsgSignature
     * @param string $sTimeStamp
     * @param string $sNonce
     * @param string $encrypt
     * @return int
     */
    private function validateSignature(string $sMsgSignature, string $sTimeStamp, string $sNonce, string $encrypt): int
    {
        $sha1 = new SHA1();
        $array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $encrypt);
        $ret = $array[0];

        if (0 !== $ret) {
            return $ret;
        }

        $signature = $array[1];
        if ($signature !== $sMsgSignature) {
            return ErrorCode::$ValidateSignatureError;
        }

        return ErrorCode::$OK;
    }
}
