<?php

namespace WechatWorkProviderBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use WechatWorkBundle\Entity\AccessTokenAware;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;

#[Route(path: '/wechat-work-provider/test')]
class TestController extends \WechatWorkBundle\Controller\TestController
{
    public function __construct(
        CorpRepository $corpRepository,
        AgentRepository $agentRepository,
        WorkService $workService,
        private readonly AuthCorpRepository $authCorpRepository,
    ) {
        parent::__construct($corpRepository, $agentRepository, $workService);
    }

    protected function getAgent(Request $request): AccessTokenAware
    {
        if ($this->authCorpRepository && $request->query->has('authCorpId')) {
            $authCorpId = $request->query->get('authCorpId');

            return $this->authCorpRepository->findOneBy(['id' => $authCorpId]);
        }

        return parent::getAgent($request);
    }
}
