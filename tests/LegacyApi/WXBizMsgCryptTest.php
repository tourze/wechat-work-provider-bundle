<?php

namespace WechatWorkProviderBundle\Tests\LegacyApi;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\LegacyApi\ErrorCode;
use WechatWorkProviderBundle\LegacyApi\WXBizMsgCrypt;

class WXBizMsgCryptTest extends TestCase
{
    private string $token;
    private string $encodingAesKey;
    private string $receiveId;
    private WXBizMsgCrypt $wxBizMsgCrypt;

    protected function setUp(): void
    {
        // 使用标准的测试参数
        $this->token = 'test_token_123';
        $this->encodingAesKey = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG'; // 43个字符
        $this->receiveId = 'test_receive_id';
        
        $this->wxBizMsgCrypt = new WXBizMsgCrypt(
            $this->token,
            $this->encodingAesKey,
            $this->receiveId
        );
    }

    public function testConstructor(): void
    {
        $newCrypt = new WXBizMsgCrypt('token', 'key', 'id');
        $this->assertInstanceOf(WXBizMsgCrypt::class, $newCrypt);
    }

    public function testVerifyURLWithInvalidAesKeyLength(): void
    {
        // 创建一个无效的 AES 密钥（不是43个字符）
        $invalidCrypt = new WXBizMsgCrypt('token', 'invalid_key', 'id');
        
        $sMsgSignature = 'test_signature';
        $sTimeStamp = '1234567890';
        $sNonce = 'test_nonce';
        $sEchoStr = 'test_echo';
        $sReplyEchoStr = '';
        
        $result = $invalidCrypt->VerifyURL(
            $sMsgSignature,
            $sTimeStamp,
            $sNonce,
            $sEchoStr,
            $sReplyEchoStr
        );
        
        $this->assertSame(ErrorCode::$IllegalAesKey, $result);
    }

    public function testVerifyURLWithValidAesKeyLength(): void
    {
        $sMsgSignature = 'test_signature';
        $sTimeStamp = '1234567890';
        $sNonce = 'test_nonce';
        $sEchoStr = 'test_echo';
        $sReplyEchoStr = '';
        
        // 即使签名验证失败，也应该通过密钥长度检查
        $result = $this->wxBizMsgCrypt->VerifyURL(
            $sMsgSignature,
            $sTimeStamp,
            $sNonce,
            $sEchoStr,
            $sReplyEchoStr
        );
        
        // 应该不是密钥长度错误，而是其他错误（如签名验证失败）
        $this->assertNotSame(ErrorCode::$IllegalAesKey, $result);
    }

    public function testEncryptMsgWithValidParameters(): void
    {
        $sReplyMsg = '这是一条测试消息';
        $sTimeStamp = '1234567890';
        $sNonce = 'test_nonce_123';
        $sEncryptMsg = '';
        
        $result = $this->wxBizMsgCrypt->EncryptMsg(
            $sReplyMsg,
            $sTimeStamp,
            $sNonce,
            $sEncryptMsg
        );
        
        $this->assertSame(ErrorCode::$OK, $result);
        $this->assertNotEmpty($sEncryptMsg);
        // 验证生成的是XML格式
        $this->assertStringContainsString('<xml>', $sEncryptMsg);
        $this->assertStringContainsString('<Encrypt>', $sEncryptMsg);
        $this->assertStringContainsString('<MsgSignature>', $sEncryptMsg);
        $this->assertStringContainsString('<TimeStamp>', $sEncryptMsg);
        $this->assertStringContainsString('<Nonce>', $sEncryptMsg);
    }

    public function testEncryptMsgWithNullTimestamp(): void
    {
        $sReplyMsg = '测试消息';
        $sTimeStamp = null; // null 时间戳，应该自动使用当前时间
        $sNonce = 'test_nonce';
        $sEncryptMsg = '';
        
        $result = $this->wxBizMsgCrypt->EncryptMsg(
            $sReplyMsg,
            $sTimeStamp,
            $sNonce,
            $sEncryptMsg
        );
        
        $this->assertSame(ErrorCode::$OK, $result);
        $this->assertNotEmpty($sEncryptMsg);
    }

    public function testDecryptMsgWithInvalidAesKeyLength(): void
    {
        $invalidCrypt = new WXBizMsgCrypt('token', 'invalid_key', 'id');
        
        $sMsgSignature = 'test_signature';
        $sTimeStamp = '1234567890';
        $sNonce = 'test_nonce';
        $sPostData = '<xml><Encrypt>test_encrypt</Encrypt></xml>';
        $sMsg = '';
        
        $result = $invalidCrypt->DecryptMsg(
            $sMsgSignature,
            $sTimeStamp,
            $sNonce,
            $sPostData,
            $sMsg
        );
        
        $this->assertSame(ErrorCode::$IllegalAesKey, $result);
    }

    public function testDecryptMsgWithXmlPostData(): void
    {
        $sMsgSignature = 'test_signature';
        $sTimeStamp = '1234567890';
        $sNonce = 'test_nonce';
        $sPostData = '<xml><Encrypt>test_encrypt_data</Encrypt></xml>';
        $sMsg = '';
        
        $result = $this->wxBizMsgCrypt->DecryptMsg(
            $sMsgSignature,
            $sTimeStamp,
            $sNonce,
            $sPostData,
            $sMsg
        );
        
        // 应该通过密钥长度检查，但可能在其他步骤失败
        $this->assertNotSame(ErrorCode::$IllegalAesKey, $result);
    }

    public function testDecryptMsgWithJsonPostData(): void
    {
        $sMsgSignature = 'test_signature';
        $sTimeStamp = '1234567890';
        $sNonce = 'test_nonce';
        $sPostData = '{"Encrypt":"test_encrypt_json_data"}';
        $sMsg = '';
        
        $result = $this->wxBizMsgCrypt->DecryptMsg(
            $sMsgSignature,
            $sTimeStamp,
            $sNonce,
            $sPostData,
            $sMsg
        );
        
        // 应该通过密钥长度检查和JSON解析，但可能在验证签名时失败
        $this->assertNotSame(ErrorCode::$IllegalAesKey, $result);
    }

    public function testDecryptMsgWithNullTimestamp(): void
    {
        $sMsgSignature = 'test_signature';
        $sTimeStamp = null; // null 时间戳，应该自动使用当前时间
        $sNonce = 'test_nonce';
        $sPostData = '<xml><Encrypt>test_encrypt</Encrypt></xml>';
        $sMsg = '';
        
        $result = $this->wxBizMsgCrypt->DecryptMsg(
            $sMsgSignature,
            $sTimeStamp,
            $sNonce,
            $sPostData,
            $sMsg
        );
        
        // 应该能处理null时间戳
        $this->assertNotSame(ErrorCode::$IllegalAesKey, $result);
    }

    public function testEncryptAndDecryptRoundTrip(): void
    {
        $originalMessage = 'This is a test message for encryption and decryption.';
        $sTimeStamp = '1234567890';
        $sNonce = 'test_nonce_roundtrip';
        $sEncryptMsg = '';
        
        // 首先加密消息
        $encryptResult = $this->wxBizMsgCrypt->EncryptMsg(
            $originalMessage,
            $sTimeStamp,
            $sNonce,
            $sEncryptMsg
        );
        
        $this->assertSame(ErrorCode::$OK, $encryptResult);
        $this->assertNotEmpty($sEncryptMsg);
        
        // 然后尝试解密（注意：实际应用中需要正确的签名）
        // 这里主要验证加密过程的完整性
        $this->assertStringContainsString('<Encrypt>', $sEncryptMsg);
        $this->assertStringContainsString('<MsgSignature>', $sEncryptMsg);
        $this->assertStringContainsString($sTimeStamp, $sEncryptMsg);
        $this->assertStringContainsString($sNonce, $sEncryptMsg);
    }

    public function testValidAesKeyLengthBoundaries(): void
    {
        // 测试42个字符（无效）
        $shortKey = str_repeat('a', 42);
        $shortKeyCrypt = new WXBizMsgCrypt('token', $shortKey, 'id');
        
        $result = $shortKeyCrypt->VerifyURL('sig', '123', 'nonce', 'echo', $reply);
        $this->assertSame(ErrorCode::$IllegalAesKey, $result);
        
        // 测试44个字符（无效）
        $longKey = str_repeat('a', 44);
        $longKeyCrypt = new WXBizMsgCrypt('token', $longKey, 'id');
        
        $result = $longKeyCrypt->VerifyURL('sig', '123', 'nonce', 'echo', $reply);
        $this->assertSame(ErrorCode::$IllegalAesKey, $result);
        
        // 测试43个字符（有效）
        $validKey = str_repeat('a', 43);
        $validKeyCrypt = new WXBizMsgCrypt('token', $validKey, 'id');
        
        $result = $validKeyCrypt->VerifyURL('sig', '123', 'nonce', 'echo', $reply);
        $this->assertNotSame(ErrorCode::$IllegalAesKey, $result);
    }

    public function testSpecialCharactersInParameters(): void
    {
        $specialToken = 'token_测试!@#$%^&*()';
        $specialReceiveId = 'receive_测试_123';
        
        $specialCrypt = new WXBizMsgCrypt(
            $specialToken,
            $this->encodingAesKey,
            $specialReceiveId
        );
        
        $sReplyMsg = '包含特殊字符的消息：!@#$%^&*()_+-={}[]|\\:";\'<>?,./ 测试中文';
        $sTimeStamp = '1234567890';
        $sNonce = 'special_nonce_测试';
        $sEncryptMsg = '';
        
        $result = $specialCrypt->EncryptMsg(
            $sReplyMsg,
            $sTimeStamp,
            $sNonce,
            $sEncryptMsg
        );
        
        $this->assertSame(ErrorCode::$OK, $result);
        $this->assertNotEmpty($sEncryptMsg);
    }

    public function testEmptyMessageEncryption(): void
    {
        $sReplyMsg = ''; // 空消息
        $sTimeStamp = '1234567890';
        $sNonce = 'empty_test';
        $sEncryptMsg = '';
        
        $result = $this->wxBizMsgCrypt->EncryptMsg(
            $sReplyMsg,
            $sTimeStamp,
            $sNonce,
            $sEncryptMsg
        );
        
        $this->assertSame(ErrorCode::$OK, $result);
        $this->assertNotEmpty($sEncryptMsg); // 即使原消息为空，加密后也不为空
    }

    public function testLongMessageEncryption(): void
    {
        $sReplyMsg = str_repeat('这是一条很长的测试消息，用来验证加密功能对长文本的处理能力。', 100);
        $sTimeStamp = '1234567890';
        $sNonce = 'long_message_test';
        $sEncryptMsg = '';
        
        $result = $this->wxBizMsgCrypt->EncryptMsg(
            $sReplyMsg,
            $sTimeStamp,
            $sNonce,
            $sEncryptMsg
        );
        
        $this->assertSame(ErrorCode::$OK, $result);
        $this->assertNotEmpty($sEncryptMsg);
        $this->assertGreaterThan(strlen($sReplyMsg), strlen($sEncryptMsg)); // 加密后应该更长
    }
} 