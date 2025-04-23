<?php

namespace WechatWorkProviderBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use WechatWorkBundle\Entity\AccessTokenAware;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Controller\TestController;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;
use WechatWorkProviderBundle\Repository\ProviderRepository;
use WechatWorkProviderBundle\Request\License\ActiveAccountRequest;
use WechatWorkProviderBundle\Service\ProviderService;

#[Route(path: '/wechat-work-provider/external-contact/test')]
class TestExternalContactController extends TestController
{
    public function __construct(
        CorpRepository $corpRepository,
        AgentRepository $agentRepository,
        WorkService $workService,
        private readonly AuthCorpRepository $authCorpRepository,
    ) {
        parent::__construct($corpRepository, $agentRepository, $workService);
    }

    #[Route('/active_account')]
    public function activeAccount(Request $request, ProviderRepository $providerRepository, ProviderService $providerService): Response
    {
        $provider = $providerRepository->findOneBy([
            'id' => $request->query->get('providerId'),
        ]);

        $newRequest = new ActiveAccountRequest();
        $newRequest->setProvider($provider);
        $newRequest->setActiveCode($request->query->get('active_code'));
        $newRequest->setCorpId($request->query->get('corpid'));
        $newRequest->setUserId($request->query->get('userid'));
        $response = $providerService->request($newRequest);

        return $this->json($response);
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
