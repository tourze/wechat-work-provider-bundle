<?php

namespace WechatWorkProviderBundle\Tests\Integration\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use WechatWorkProviderBundle\Controller\CorpCallbackController;

class CorpCallbackControllerTest extends TestCase
{
    public function testControllerCanBeInstantiated(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $controller = new CorpCallbackController($entityManager);
        $this->assertInstanceOf(CorpCallbackController::class, $controller);
    }
}