<?php

namespace WechatWorkProviderBundle\Tests\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;
use WechatWorkProviderBundle\Event\CorpServerMessageResponseEvent;

/**
 * @internal
 */
#[CoversClass(CorpServerMessageResponseEvent::class)]
final class CorpServerMessageResponseEventTest extends AbstractEventTestCase
{
}
