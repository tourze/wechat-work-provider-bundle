<?php

namespace WechatWorkProviderBundle\Tests\LegacyApi;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\LegacyApi\CryptResult;
use WechatWorkProviderBundle\LegacyApi\ErrorCode;

/**
 * @internal
 */
#[CoversClass(CryptResult::class)]
final class CryptResultTest extends TestCase
{
    public function testConstructor(): void
    {
        $result = new CryptResult(ErrorCode::$OK, 'test data');

        $this->assertSame(ErrorCode::$OK, $result->errorCode);
        $this->assertSame('test data', $result->data);
    }

    public function testConstructorWithDefaultData(): void
    {
        $result = new CryptResult(ErrorCode::$ValidateSignatureError);

        $this->assertSame(ErrorCode::$ValidateSignatureError, $result->errorCode);
        $this->assertSame('', $result->data);
    }

    public function testSuccessFactory(): void
    {
        $result = CryptResult::success('success data');

        $this->assertSame(ErrorCode::$OK, $result->errorCode);
        $this->assertSame('success data', $result->data);
        $this->assertTrue($result->isSuccess());
        $this->assertFalse($result->isError());
    }

    public function testSuccessFactoryWithDefaultData(): void
    {
        $result = CryptResult::success();

        $this->assertSame(ErrorCode::$OK, $result->errorCode);
        $this->assertSame('', $result->data);
        $this->assertTrue($result->isSuccess());
        $this->assertFalse($result->isError());
    }

    public function testErrorFactory(): void
    {
        $result = CryptResult::error(ErrorCode::$IllegalAesKey);

        $this->assertSame(ErrorCode::$IllegalAesKey, $result->errorCode);
        $this->assertSame('', $result->data);
        $this->assertFalse($result->isSuccess());
        $this->assertTrue($result->isError());
    }

    public function testIsSuccess(): void
    {
        $successResult = new CryptResult(ErrorCode::$OK, 'data');
        $this->assertTrue($successResult->isSuccess());

        $errorResult = new CryptResult(ErrorCode::$ValidateSignatureError);
        $this->assertFalse($errorResult->isSuccess());
    }

    public function testIsError(): void
    {
        $successResult = new CryptResult(ErrorCode::$OK, 'data');
        $this->assertFalse($successResult->isError());

        $errorResult = new CryptResult(ErrorCode::$IllegalAesKey);
        $this->assertTrue($errorResult->isError());
    }

    public function testReadonlyProperties(): void
    {
        $result = new CryptResult(ErrorCode::$OK, 'test');

        // 验证属性是只读的（如果尝试修改会产生错误）
        $this->assertSame(ErrorCode::$OK, $result->errorCode);
        $this->assertSame('test', $result->data);
    }

    public function testVariousErrorCodes(): void
    {
        $errorCodes = [
            ErrorCode::$ValidateSignatureError,
            ErrorCode::$ParseXmlError,
            ErrorCode::$ComputeSignatureError,
            ErrorCode::$IllegalAesKey,
            ErrorCode::$ValidateCorpidError,
            ErrorCode::$EncryptAESError,
            ErrorCode::$DecryptAESError,
            ErrorCode::$IllegalBuffer,
            ErrorCode::$EncodeBase64Error,
            ErrorCode::$DecodeBase64Error,
            ErrorCode::$GenReturnXmlError,
        ];

        foreach ($errorCodes as $errorCode) {
            $result = CryptResult::error($errorCode);
            $this->assertSame($errorCode, $result->errorCode);
            $this->assertTrue($result->isError());
            $this->assertFalse($result->isSuccess());
        }
    }
}
