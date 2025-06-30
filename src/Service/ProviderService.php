<?php

namespace WechatWorkProviderBundle\Service;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Client\ClientTrait;
use HttpClientBundle\Exception\HttpClientException;
use HttpClientBundle\Request\RequestInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Exception\AccessTokenException;
use WechatWorkProviderBundle\Request\GetCorpTokenRequest;
use WechatWorkProviderBundle\Request\GetProviderTokenRequest;
use WechatWorkProviderBundle\Request\GetSuiteTokenRequest;
use WechatWorkProviderBundle\Request\WithAuthCorpRequest;
use WechatWorkProviderBundle\Request\WithProviderRequest;
use WechatWorkProviderBundle\Request\WithSuiteRequest;
use Yiisoft\Json\Json;

class ProviderService extends ApiClient
{
    use ClientTrait;

    public function __construct(
        private readonly CorpRepository $corpRepository,
        private readonly AgentRepository $agentRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getBaseUrl(): string
    {
        return 'https://qyapi.weixin.qq.com';
    }

    /**
     * 把授权的企业微信信息，同步一份到企业微信模块
     */
    public function syncAuthCorpToCorpAndAgent(AuthCorp $authCorp): Corp
    {
        $corp = $this->corpRepository->findOneBy([
            'corpId' => $authCorp->getCorpId(),
        ]);
        if ($corp === null) {
            $corp = new Corp();
            $corp->setCorpId($authCorp->getCorpId());
        }
        $corp->setFromProvider(true);

        $corp->setName($authCorp->getCorpName());
        $this->entityManager->persist($corp);
        $this->entityManager->flush();

        // 添加应用信息
        $agents = $authCorp->getAuthInfo();
        if (isset($agents['agent'])) {
            $agents = $agents['agent'];
        }
        foreach ($agents as $item) {
            $agent = $this->agentRepository->findOneBy([
                'corp' => $corp,
                'agentId' => $item['agentid'],
            ]);
            if ($agent === null) {
                $agent = new Agent();
                $agent->setCorp($corp);
                $agent->setAgentId($item['agentid']);
            }
            $agent->setName($item['name']);
            $agent->setSquareLogoUrl($item['square_logo_url']);
            $agent->setAccessToken($authCorp->getAccessToken());
            $agent->setAccessTokenExpireTime($authCorp->getTokenExpireTime());
            $this->entityManager->persist($agent);
            $this->entityManager->flush();
        }

        return $corp;
    }

    protected function getRequestMethod(RequestInterface $request): string
    {
        return $request->getRequestMethod() ?? 'POST';
    }

    protected function getRequestOptions(RequestInterface $request): ?array
    {
        $options = $request->getRequestOptions();
        if (!isset($options['query'])) {
            $options['query'] = [];
        }

        if ($request instanceof WithSuiteRequest) {
            $suite = $request->getSuite();
            $now = CarbonImmutable::now();

            if ($suite->getTokenExpireTime() === null) {
                $suite->setTokenExpireTime(CarbonImmutable::now()->lastOfYear());
            }
            if ($suite->getSuiteAccessToken() !== null && $suite->getSuiteAccessToken() !== '' && $now->greaterThan($suite->getTokenExpireTime())) {
                $suite->setSuiteAccessToken('');
            }

            if ($suite->getSuiteAccessToken() === null || $suite->getSuiteAccessToken() === '') {
                $tokenRequest = new GetSuiteTokenRequest();
                $tokenRequest->setSuiteId($suite->getSuiteId());
                $tokenRequest->setSuiteSecret($suite->getSuiteSecret());
                $tokenRequest->setSuiteTicket($suite->getSuiteTicket());
                $tokenResponse = $this->request($tokenRequest);
                $suite->setSuiteAccessToken($tokenResponse['suite_access_token']);
                $suite->setTokenExpireTime(CarbonImmutable::now()->addSeconds($tokenResponse['expires_in']));
                $this->entityManager->persist($suite);
                $this->entityManager->flush();
            }

            $options['query']['suite_access_token'] = $suite->getSuiteAccessToken();
        }

        if ($request instanceof WithAuthCorpRequest) {
            $authCorp = $request->getAuthCorp();
            $now = CarbonImmutable::now();

            if ($authCorp->getTokenExpireTime() === null) {
                $authCorp->setTokenExpireTime(CarbonImmutable::now()->lastOfYear());
            }
            if ($authCorp->getAccessToken() !== null && $authCorp->getAccessToken() !== '' && $now->greaterThan($authCorp->getTokenExpireTime())) {
                $authCorp->setAccessToken('');
            }

            if ($authCorp->getAccessToken() === null || $authCorp->getAccessToken() === '') {
                $tokenRequest = new GetCorpTokenRequest();
                $tokenRequest->setAuthCorpId($authCorp->getCorpId());
                $tokenRequest->setPermanentCode($authCorp->getPermanentCode());
                $tokenResponse = $this->request($tokenRequest);
                if (!isset($tokenResponse['access_token'])) {
                    $this->apiClientLogger?->error('access_token结果异常', [
                        'authCorp' => $authCorp,
                        'tokenResponse' => $tokenResponse,
                    ]);
                    throw new AccessTokenException('无法获取应用AccessToken');
                }

                $authCorp->setAccessToken($tokenResponse['access_token']);
                $authCorp->setTokenExpireTime(CarbonImmutable::now()->addSeconds($tokenResponse['expires_in']));
                $this->entityManager->persist($authCorp);
                $this->entityManager->flush();
            }

            $options['query']['access_token'] = $authCorp->getAccessToken();
        }

        if ($request instanceof WithProviderRequest) {
            $provider = $request->getProvider();
            $now = CarbonImmutable::now();

            if ($provider->getTokenExpireTime() === null) {
                $provider->setTokenExpireTime(CarbonImmutable::now()->lastOfYear());
            }
            if ($provider->getProviderAccessToken() !== null && $provider->getProviderAccessToken() !== '' && $now->greaterThanOrEqualTo($provider->getTokenExpireTime())) {
                $provider->setProviderAccessToken('');
            }

            if ($provider->getProviderAccessToken() === null || $provider->getProviderAccessToken() === '') {
                $tokenRequest = new GetProviderTokenRequest();
                $tokenRequest->setCorpId($provider->getCorpId());
                $tokenRequest->setProviderSecret($provider->getProviderSecret());
                $tokenResponse = $this->request($tokenRequest);
                $provider->setProviderAccessToken($tokenResponse['provider_access_token']);
                $provider->setTokenExpireTime(CarbonImmutable::now()->addSeconds($tokenResponse['expires_in']));
                $this->entityManager->persist($provider);
                $this->entityManager->flush();
            }

            $options['query']['provider_access_token'] = $provider->getProviderAccessToken();
        }

        return $options;
    }

    protected function formatResponse(RequestInterface $request, ResponseInterface $response): mixed
    {
        $json = $response->getContent();
        $json = Json::decode($json);

        if (isset($json['errcode'])) {
            if (0 !== $json['errcode']) {
                throw new HttpClientException($request, $response, $json['errmsg'], $json['errcode']);
            }
        }

        return $json;
    }
}
