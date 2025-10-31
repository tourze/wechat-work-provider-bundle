<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatWorkProviderBundle\WechatWorkProviderBundle;

/**
 * @internal
 */
#[CoversClass(WechatWorkProviderBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatWorkProviderBundleTest extends AbstractBundleTestCase
{
}
