<?php

namespace WechatWorkProviderBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatWorkProviderBundle\Command\SyncWechatWorkCorpInfoCommand;

/**
 * @internal
 */
#[CoversClass(SyncWechatWorkCorpInfoCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncWechatWorkCorpInfoCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // Command 测试的自定义初始化逻辑
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncWechatWorkCorpInfoCommand::class);

        return new CommandTester($command);
    }

    public function testCommandCanBeInstantiated(): void
    {
        $command = self::getService(SyncWechatWorkCorpInfoCommand::class);
        $this->assertInstanceOf(SyncWechatWorkCorpInfoCommand::class, $command);
    }

    public function testCommandImplementsInterface(): void
    {
        $reflection = new \ReflectionClass(SyncWechatWorkCorpInfoCommand::class);
        $this->assertTrue($reflection->isSubclassOf(Command::class));
    }

    public function testCommandTesterCanBeUsed(): void
    {
        $mockCommand = $this->createMock(Command::class);
        $tester = new CommandTester($mockCommand);
        $this->assertInstanceOf(CommandTester::class, $tester);
    }

    public function testCommandHasConfigureMethod(): void
    {
        $reflection = new \ReflectionClass(SyncWechatWorkCorpInfoCommand::class);
        $this->assertTrue($reflection->hasMethod('configure'));
    }

    public function testCommandHasExecuteMethod(): void
    {
        $reflection = new \ReflectionClass(SyncWechatWorkCorpInfoCommand::class);
        $this->assertTrue($reflection->hasMethod('execute'));
    }
}
