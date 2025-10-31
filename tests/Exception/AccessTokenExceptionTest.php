<?php

namespace WechatWorkProviderBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatWorkProviderBundle\Exception\AccessTokenException;

/**
 * @internal
 */
#[CoversClass(AccessTokenException::class)]
final class AccessTokenExceptionTest extends AbstractExceptionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new AccessTokenException();
        $this->assertInstanceOf(AccessTokenException::class, $exception);
    }
}
