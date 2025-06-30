<?php

namespace WechatWorkProviderBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Exception\AccessTokenException;

class AccessTokenExceptionTest extends TestCase
{
    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new AccessTokenException();
        $this->assertInstanceOf(AccessTokenException::class, $exception);
    }
}