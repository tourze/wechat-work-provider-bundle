<?php

namespace WechatWorkProviderBundle\Tests\Integration\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Controller\ProviderCallbackController;

class ProviderCallbackControllerTest extends TestCase
{
    public function testControllerCanBeInstantiated(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $controller = new ProviderCallbackController($entityManager);
        $this->assertInstanceOf(ProviderCallbackController::class, $controller);
    }
}