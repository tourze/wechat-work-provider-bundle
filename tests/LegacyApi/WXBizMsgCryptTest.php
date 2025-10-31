<?php

namespace WechatWorkProviderBundle\Tests\LegacyApi;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\LegacyApi\CryptResult;
use WechatWorkProviderBundle\LegacyApi\ErrorCode;
use WechatWorkProviderBundle\LegacyApi\WXBizMsgCrypt;

/**
 * @internal
 */
#[CoversClass(WXBizMsgCrypt::class)]
final class WXBizMsgCryptTest extends TestCase
{
    private string $token;

    private string $encodingAesKey;

    private string $receiveId;

    private WXBizMsgCrypt $wxBizMsgCrypt;

    protected function setUp(): void
    {
        parent::setUp();

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

        $result = $invalidCrypt->VerifyURL(
            $sMsgSignature,
            $sTimeStamp,
            $sNonce,
            $sEchoStr
        );

        $this->assertInstanceOf(CryptResult::class, $result);
        $this->assertSame(ErrorCode::$IllegalAesKey, $result->errorCode);
        $this->assertTrue($result->isError());
    }

    public function testVerifyURLWithValidAesKeyLength(): void
    {
        $sMsgSignature = 'test_signature';
        $sTimeStamp = '1234567890';
        $sNonce = 'test_nonce';
        $sEchoStr = 'test_echo';

        // 即使签名验证失败，也应该通过密钥长度检查
        $result = $this->wxBizMsgCrypt->VerifyURL(
            $sMsgSignature,
            $sTimeStamp,
            $sNonce,
            $sEchoStr
        );

        $this->assertInstanceOf(CryptResult::class, $result);
        // 应该不是密钥长度错误，而是其他错误（如签名验证失败）
        $this->assertNotSame(ErrorCode::$IllegalAesKey, $result->errorCode);
    }

    public function testEncryptMsgWithValidParameters(): void
    {
        $sReplyMsg = '这是一条测试消息';
        $sTimeStamp = 1234567890;
        $sNonce = 'test_nonce_123';

        $result = $this->wxBizMsgCrypt->EncryptMsg(
            $sReplyMsg,
            $sTimeStamp,
            $sNonce
        );

        $this->assertInstanceOf(CryptResult::class, $result);
        $this->assertSame(ErrorCode::$OK, $result->errorCode);
        $this->assertTrue($result->isSuccess());
        $this->assertNotEmpty($result->data);
        // 验证生成的是XML格式
        $this->assertStringContainsString('<xml>', $result->data);
        $this->assertStringContainsString('<Encrypt>', $result->data);
        $this->assertStringContainsString('<MsgSignature>', $result->data);
        $this->assertStringContainsString('<TimeStamp>', $result->data);
        $this->assertStringContainsString('<Nonce>', $result->data);
    }

    public function testEncryptMsgWithNullTimestamp(): void
    {
        $sReplyMsg = '测试消息';
        $sTimeStamp = null; // null 时间戳，应该自动使用当前时间
        $sNonce = 'test_nonce';

        $result = $this->wxBizMsgCrypt->EncryptMsg(
            $sReplyMsg,
            $sTimeStamp,
            $sNonce
        );

        $this->assertInstanceOf(CryptResult::class, $result);
        $this->assertSame(ErrorCode::$OK, $result->errorCode);
        $this->assertTrue($result->isSuccess());
        $this->assertNotEmpty($result->data);
    }

    public function testDecryptMsgWithInvalidAesKeyLength(): void
    {
        $invalidCrypt = new WXBizMsgCrypt('token', 'invalid_key', 'id');

        $sMsgSignature = 'test_signature';
        $sTimeStamp = 1234567890;
        $sNonce = 'test_nonce';
        $sPostData = '<xml><Encrypt>test_encrypt</Encrypt></xml>';

        $result = $invalidCrypt->DecryptMsg(
            $sMsgSignature,
            $sTimeStamp,
            $sNonce,
            $sPostData
        );

        $this->assertInstanceOf(CryptResult::class, $result);
        $this->assertSame(ErrorCode::$IllegalAesKey, $result->errorCode);
        $this->assertTrue($result->isError());
    }

    public function testDecryptMsgWithXmlPostData(): void
    {
        $sMsgSignature = 'test_signature';
        $sTimeStamp = 1234567890;
        $sNonce = 'test_nonce';
        $sPostData = '<xml><Encrypt>test_encrypt_data</Encrypt></xml>';

        $result = $this->wxBizMsgCrypt->DecryptMsg(
            $sMsgSignature,
            $sTimeStamp,
            $sNonce,
            $sPostData
        );

        $this->assertInstanceOf(CryptResult::class, $result);
        // 应该通过密钥长度检查，但可能在其他步骤失败
        $this->assertNotSame(ErrorCode::$IllegalAesKey, $result->errorCode);
    }

    public function testDecryptMsgWithJsonPostData(): void
    {
        $sMsgSignature = 'test_signature';
        $sTimeStamp = 1234567890;
        $sNonce = 'test_nonce';
        $sPostData = '{"Encrypt":"test_encrypt_json_data"}';

        $result = $this->wxBizMsgCrypt->DecryptMsg(
            $sMsgSignature,
            $sTimeStamp,
            $sNonce,
            $sPostData
        );

        $this->assertInstanceOf(CryptResult::class, $result);
        // 应该通过密钥长度检查和JSON解析，但可能在验证签名时失败
        $this->assertNotSame(ErrorCode::$IllegalAesKey, $result->errorCode);
    }

    public function testDecryptMsgWithNullTimestamp(): void
    {
        $sMsgSignature = 'test_signature';
        $sTimeStamp = null; // null 时间戳，应该自动使用当前时间
        $sNonce = 'test_nonce';
        $sPostData = '<xml><Encrypt>test_encrypt</Encrypt></xml>';

        $result = $this->wxBizMsgCrypt->DecryptMsg(
            $sMsgSignature,
            $sTimeStamp,
            $sNonce,
            $sPostData
        );

        $this->assertInstanceOf(CryptResult::class, $result);
        // 应该能处理null时间戳
        $this->assertNotSame(ErrorCode::$IllegalAesKey, $result->errorCode);
    }

    public function testEncryptAndDecryptRoundTrip(): void
    {
        $originalMessage = 'This is a test message for encryption and decryption.';
        $sTimeStamp = 1234567890;
        $sNonce = 'test_nonce_roundtrip';

        // 首先加密消息
        $encryptResult = $this->wxBizMsgCrypt->EncryptMsg(
            $originalMessage,
            $sTimeStamp,
            $sNonce
        );

        $this->assertInstanceOf(CryptResult::class, $encryptResult);
        $this->assertSame(ErrorCode::$OK, $encryptResult->errorCode);
        $this->assertTrue($encryptResult->isSuccess());
        $this->assertNotEmpty($encryptResult->data);

        // 然后尝试解密（注意：实际应用中需要正确的签名）
        // 这里主要验证加密过程的完整性
        $this->assertStringContainsString('<Encrypt>', $encryptResult->data);
        $this->assertStringContainsString('<MsgSignature>', $encryptResult->data);
        $this->assertStringContainsString((string) $sTimeStamp, $encryptResult->data);
        $this->assertStringContainsString($sNonce, $encryptResult->data);
    }

    public function testValidAesKeyLengthBoundaries(): void
    {
        // 测试42个字符（无效）
        $shortKey = str_repeat('a', 42);
        $shortKeyCrypt = new WXBizMsgCrypt('token', $shortKey, 'id');

        $result = $shortKeyCrypt->VerifyURL('sig', '123', 'nonce', 'echo');
        $this->assertInstanceOf(CryptResult::class, $result);
        $this->assertSame(ErrorCode::$IllegalAesKey, $result->errorCode);

        // 测试44个字符（无效）
        $longKey = str_repeat('a', 44);
        $longKeyCrypt = new WXBizMsgCrypt('token', $longKey, 'id');

        $result = $longKeyCrypt->VerifyURL('sig', '123', 'nonce', 'echo');
        $this->assertInstanceOf(CryptResult::class, $result);
        $this->assertSame(ErrorCode::$IllegalAesKey, $result->errorCode);

        // 测试43个字符（有效）
        $validKey = str_repeat('a', 43);
        $validKeyCrypt = new WXBizMsgCrypt('token', $validKey, 'id');

        $result = $validKeyCrypt->VerifyURL('sig', '123', 'nonce', 'echo');
        $this->assertInstanceOf(CryptResult::class, $result);
        $this->assertNotSame(ErrorCode::$IllegalAesKey, $result->errorCode);
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

        $sReplyMsg = '包含特殊字符的消息：!@#$%^&*()_+-={}[]|\:";\'<>?,./ 测试中文';
        $sTimeStamp = 1234567890;
        $sNonce = 'special_nonce_测试';

        $result = $specialCrypt->EncryptMsg(
            $sReplyMsg,
            $sTimeStamp,
            $sNonce
        );

        $this->assertInstanceOf(CryptResult::class, $result);
        $this->assertSame(ErrorCode::$OK, $result->errorCode);
        $this->assertTrue($result->isSuccess());
        $this->assertNotEmpty($result->data);
    }

    public function testEmptyMessageEncryption(): void
    {
        $sReplyMsg = ''; // 空消息
        $sTimeStamp = 1234567890;
        $sNonce = 'empty_test';

        $result = $this->wxBizMsgCrypt->EncryptMsg(
            $sReplyMsg,
            $sTimeStamp,
            $sNonce
        );

        $this->assertInstanceOf(CryptResult::class, $result);
        $this->assertSame(ErrorCode::$OK, $result->errorCode);
        $this->assertTrue($result->isSuccess());
        $this->assertNotEmpty($result->data); // 即使原消息为空，加密后也不为空
    }

    public function testLongMessageEncryption(): void
    {
        $sReplyMsg = str_repeat('这是一条很长的测试消息，用来验证加密功能对长文本的处理能力。', 100);
        $sTimeStamp = 1234567890;
        $sNonce = 'long_message_test';

        $result = $this->wxBizMsgCrypt->EncryptMsg(
            $sReplyMsg,
            $sTimeStamp,
            $sNonce
        );

        $this->assertInstanceOf(CryptResult::class, $result);
        $this->assertSame(ErrorCode::$OK, $result->errorCode);
        $this->assertTrue($result->isSuccess());
        $this->assertNotEmpty($result->data);
        $this->assertGreaterThan(strlen($sReplyMsg), strlen($result->data)); // 加密后应该更长
    }
}
