<?php

namespace WechatWorkProviderBundle\Tests\Integration\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Controller\SuiteCallbackController;

class SuiteCallbackControllerTest extends TestCase
{
    public function testControllerCanBeInstantiated(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $controller = new SuiteCallbackController($entityManager);
        $this->assertInstanceOf(SuiteCallbackController::class, $controller);
    }
}