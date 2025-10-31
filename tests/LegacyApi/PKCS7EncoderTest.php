<?php

namespace WechatWorkProviderBundle\Tests\LegacyApi;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\LegacyApi\PKCS7Encoder;

/**
 * @internal
 */
#[CoversClass(PKCS7Encoder::class)]
final class PKCS7EncoderTest extends TestCase
{
    private PKCS7Encoder $encoder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->encoder = new PKCS7Encoder();
    }

    public function testBlockSizeIsSetCorrectly(): void
    {
        $this->assertSame(32, PKCS7Encoder::getBlockSize());
    }

    public function testEncodeWithEmptyString(): void
    {
        $text = '';
        $encoded = $this->encoder->encode($text);

        // 空字符串应该填充满一个块(32字节)
        $this->assertSame(32, strlen($encoded));

        // 所有填充字节都应该是 chr(32)
        for ($i = 0; $i < 32; ++$i) {
            $this->assertSame(chr(32), $encoded[$i]);
        }
    }

    public function testEncodeWithSingleCharacter(): void
    {
        $text = 'A';
        $encoded = $this->encoder->encode($text);

        // 1个字符 + 31个填充字节 = 32字节
        $this->assertSame(32, strlen($encoded));
        $this->assertSame('A', $encoded[0]);

        // 检查填充字节
        for ($i = 1; $i < 32; ++$i) {
            $this->assertSame(chr(31), $encoded[$i]);
        }
    }

    public function testEncodeWithExactBlockSize(): void
    {
        $text = str_repeat('X', 32); // 正好32字节
        $encoded = $this->encoder->encode($text);

        // 应该再填充一个完整的块
        $this->assertSame(64, strlen($encoded));
        $this->assertSame($text, substr($encoded, 0, 32));

        // 检查填充的第二个块
        for ($i = 32; $i < 64; ++$i) {
            $this->assertSame(chr(32), $encoded[$i]);
        }
    }

    public function testEncodeWithPartialBlock(): void
    {
        $text = str_repeat('Y', 10); // 10字节
        $encoded = $this->encoder->encode($text);

        // 10字节 + 22字节填充 = 32字节
        $this->assertSame(32, strlen($encoded));
        $this->assertSame($text, substr($encoded, 0, 10));

        // 检查填充字节（应该是22个chr(22)）
        for ($i = 10; $i < 32; ++$i) {
            $this->assertSame(chr(22), $encoded[$i]);
        }
    }

    public function testEncodeWithMultipleBlocks(): void
    {
        $text = str_repeat('Z', 50); // 50字节，超过一个块
        $encoded = $this->encoder->encode($text);

        // 50字节 + 14字节填充 = 64字节 (2个块)
        $this->assertSame(64, strlen($encoded));
        $this->assertSame($text, substr($encoded, 0, 50));

        // 检查填充字节（应该是14个chr(14)）
        for ($i = 50; $i < 64; ++$i) {
            $this->assertSame(chr(14), $encoded[$i]);
        }
    }

    public function testDecodeEncodedEmptyString(): void
    {
        $original = '';
        $encoded = $this->encoder->encode($original);
        $decoded = $this->encoder->decode($encoded);

        $this->assertSame($original, $decoded);
    }

    public function testDecodeEncodedSingleCharacter(): void
    {
        $original = 'A';
        $encoded = $this->encoder->encode($original);
        $decoded = $this->encoder->decode($encoded);

        $this->assertSame($original, $decoded);
    }

    public function testDecodeEncodedText(): void
    {
        $original = 'Hello, World!';
        $encoded = $this->encoder->encode($original);
        $decoded = $this->encoder->decode($encoded);

        $this->assertSame($original, $decoded);
    }

    public function testDecodeEncodedLongText(): void
    {
        $original = str_repeat('This is a test message. ', 10); // 240字节
        $encoded = $this->encoder->encode($original);
        $decoded = $this->encoder->decode($encoded);

        $this->assertSame($original, $decoded);
    }

    public function testDecodeEncodedExactBlockSize(): void
    {
        $original = str_repeat('X', 32);
        $encoded = $this->encoder->encode($original);
        $decoded = $this->encoder->decode($encoded);

        $this->assertSame($original, $decoded);
    }

    public function testDecodeWithInvalidPadding(): void
    {
        // 创建一个无效的填充字符串
        $invalid = str_repeat('A', 31) . chr(0); // 最后一个字符是无效的填充
        $decoded = $this->encoder->decode($invalid);

        // 当填充无效时，应该不移除任何字符
        $this->assertSame($invalid, $decoded);
    }

    public function testDecodeWithTooLargePadding(): void
    {
        // 创建一个填充值超过块大小的字符串
        $invalid = str_repeat('A', 31) . chr(33); // 填充值33 > 32
        $decoded = $this->encoder->decode($invalid);

        // 当填充值过大时，应该不移除任何字符
        $this->assertSame($invalid, $decoded);
    }

    public function testDecodeWithEmptyString(): void
    {
        $decoded = $this->encoder->decode('');
        $this->assertSame('', $decoded);
    }

    public function testDecodeWithSingleByte(): void
    {
        $input = chr(1);
        $decoded = $this->encoder->decode($input);

        // 单字节输入，填充值为1，应该移除1个字节，结果为空
        $this->assertSame('', $decoded);
    }

    public function testEncodingDecodingRoundTrip(): void
    {
        $testStrings = [
            '',
            'A',
            'Hello',
            'Hello, World!',
            str_repeat('X', 31),
            str_repeat('X', 32),
            str_repeat('X', 33),
            str_repeat('Test message ', 20),
            '中文测试',
            "Special chars: !@#$%^&*()\n\t\r",
        ];

        foreach ($testStrings as $original) {
            $encoded = $this->encoder->encode($original);
            $decoded = $this->encoder->decode($encoded);

            $this->assertSame($original, $decoded, 'Failed for string: ' . var_export($original, true));
        }
    }

    public function testPaddingIsCorrect(): void
    {
        for ($length = 1; $length <= 64; ++$length) {
            $text = str_repeat('A', $length);
            $encoded = $this->encoder->encode($text);

            // 检查编码后的长度是32的倍数
            $this->assertSame(0, strlen($encoded) % 32, "Length {$length} should result in multiple of 32");

            // 检查最后一个字节表示正确的填充长度
            $lastByte = ord($encoded[strlen($encoded) - 1]);
            $expectedPadding = 32 - ($length % 32);
            if (32 === $expectedPadding) {
                $expectedPadding = 32; // 当长度是32的倍数时，添加完整的填充块
            }

            $this->assertSame($expectedPadding, $lastByte, "Padding incorrect for length {$length}");
        }
    }
}
