<?php

namespace WechatWorkProviderBundle\LegacyApi;

class WXBizMsgCrypt
{
    private $m_sToken;

    private $m_sEncodingAesKey;

    private $m_sReceiveId;

    /**
     * 构造函数
     *
     * @param $token          string 开发者设置的token
     * @param $encodingAesKey string 开发者设置的EncodingAESKey
     * @param $receiveId      string, 不同应用场景传不同的id
     */
    public function __construct($token, $encodingAesKey, $receiveId)
    {
        $this->m_sToken = $token;
        $this->m_sEncodingAesKey = $encodingAesKey;
        $this->m_sReceiveId = $receiveId;
    }

    /*
    *验证URL
    *@param sMsgSignature: 签名串，对应URL参数的msg_signature
    *@param sTimeStamp: 时间戳，对应URL参数的timestamp
    *@param sNonce: 随机串，对应URL参数的nonce
    *@param sEchoStr: 随机串，对应URL参数的echostr
    *@param sReplyEchoStr: 解密之后的echostr，当return返回0时有效
    *@return：成功0，失败返回对应的错误码
    */
    public function VerifyURL($sMsgSignature, $sTimeStamp, $sNonce, $sEchoStr, &$sReplyEchoStr)
    {
        if (43 != strlen($this->m_sEncodingAesKey)) {
            return ErrorCode::$IllegalAesKey;
        }

        $pc = new Prpcrypt($this->m_sEncodingAesKey);
        // verify msg_signature
        $sha1 = new SHA1();
        $array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $sEchoStr);
        $ret = $array[0];

        if (0 != $ret) {
            return $ret;
        }

        $signature = $array[1];
        if ($signature != $sMsgSignature) {
            return ErrorCode::$ValidateSignatureError;
        }

        $result = $pc->decrypt($sEchoStr, $this->m_sReceiveId);
        if (0 != $result[0]) {
            return $result[0];
        }
        $sReplyEchoStr = $result[1];

        return ErrorCode::$OK;
    }

    /**
     * 将公众平台回复用户的消息加密打包.
     * <ol>
     *    <li>对要发送的消息进行AES-CBC加密</li>
     *    <li>生成安全签名</li>
     *    <li>将消息密文和安全签名打包成xml格式</li>
     * </ol>
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function EncryptMsg($sReplyMsg, $sTimeStamp, $sNonce, &$sEncryptMsg)
    {
        $pc = new Prpcrypt($this->m_sEncodingAesKey);

        // 加密
        $array = $pc->encrypt($sReplyMsg, $this->m_sReceiveId);
        $ret = $array[0];
        if (0 != $ret) {
            return $ret;
        }

        if (null == $sTimeStamp) {
            $sTimeStamp = time();
        }
        $encrypt = $array[1];

        // 生成安全签名
        $sha1 = new SHA1();
        $array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $encrypt);
        $ret = $array[0];
        if (0 != $ret) {
            return $ret;
        }
        $signature = $array[1];

        // 生成发送的xml
        $xmlparse = new XMLParse();
        $sEncryptMsg = $xmlparse->generate($encrypt, $signature, $sTimeStamp, $sNonce);

        return ErrorCode::$OK;
    }

    /**
     * 检验消息的真实性，并且获取解密后的明文.
     * <ol>
     *    <li>利用收到的密文生成安全签名，进行签名验证</li>
     *    <li>若验证通过，则提取xml中的加密消息</li>
     *    <li>对消息进行解密</li>
     * </ol>
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function DecryptMsg($sMsgSignature, $sTimeStamp, $sNonce, $sPostData, &$sMsg)
    {
        if (43 != strlen($this->m_sEncodingAesKey)) {
            return ErrorCode::$IllegalAesKey;
        }

        $pc = new Prpcrypt($this->m_sEncodingAesKey);

        if (json_validate($sPostData)) {
            $sPostData = json_decode($sPostData, true);
            $encrypt = $sPostData['Encrypt'];
        } else {
            // 提取密文
            $xmlparse = new XMLParse();
            $array = $xmlparse->extract($sPostData);
            $ret = $array[0];

            if (0 != $ret) {
                return intval($ret);
            }
            $encrypt = $array[1];
        }

        if (null == $sTimeStamp) {
            $sTimeStamp = time();
        }

        // 验证安全签名
        $sha1 = new SHA1();
        $array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $encrypt);
        $ret = $array[0];

        if (0 != $ret) {
            return $ret;
        }

        $signature = $array[1];
        if ($signature != $sMsgSignature) {
            return ErrorCode::$ValidateSignatureError;
        }

        $result = $pc->decrypt($encrypt, $this->m_sReceiveId);
        if (0 != $result[0]) {
            return $result[0];
        }
        $sMsg = $result[1];

        return ErrorCode::$OK;
    }
}
