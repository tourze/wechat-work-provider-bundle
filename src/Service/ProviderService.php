<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Exception\GeneralHttpClientException;
use HttpClientBundle\Request\RequestInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Request\WithAuthCorpRequest;
use WechatWorkProviderBundle\Request\WithProviderRequest;
use WechatWorkProviderBundle\Request\WithSuiteRequest;
use WechatWorkProviderBundle\Service\TokenManager;
use Yiisoft\Json\Json;

#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'wechat_work_provider')]
class ProviderService extends ApiClient implements RequestClientInterface
{
    private ?TokenManager $tokenManager = null;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly CorpRepository $corpRepository,
        private readonly AgentRepository $agentRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly HttpClientInterface $httpClient,
        private readonly LockFactory $lockFactory,
        private readonly CacheInterface $cache,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly AsyncInsertService $asyncInsertService,
    ) {
    }

    public function setTokenManager(TokenManager $tokenManager): void
    {
        $this->tokenManager = $tokenManager;
    }

    private function getTokenManager(): TokenManager
    {
        if (null === $this->tokenManager) {
            $this->tokenManager = new TokenManager($this->logger, $this->entityManager, $this);
        }

        return $this->tokenManager;
    }

    protected function getLockFactory(): LockFactory
    {
        return $this->lockFactory;
    }

    protected function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function getCache(): CacheInterface
    {
        return $this->cache;
    }

    protected function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    protected function getAsyncInsertService(): AsyncInsertService
    {
        return $this->asyncInsertService;
    }

    public function getBaseUrl(): string
    {
        return 'https://qyapi.weixin.qq.com';
    }

    /**
     * 优先使用Request中定义的地址
     */
    protected function getRequestUrl(RequestInterface $request): string
    {
        $path = ltrim($request->getRequestPath(), '/');
        if (str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, 'http://')) {
            return $path;
        }

        $domain = trim($this->getBaseUrl());
        if ('' === $domain) {
            throw new \RuntimeException(self::class . '缺少getBaseUrl的定义');
        }

        return "{$domain}/{$path}";
    }

    /**
     * 把授权的企业微信信息，同步一份到企业微信模块
     */
    public function syncAuthCorpToCorpAndAgent(AuthCorp $authCorp): Corp
    {
        $corp = $this->findOrCreateCorp($authCorp);
        $this->syncAgents($authCorp, $corp);

        return $corp;
    }

    private function findOrCreateCorp(AuthCorp $authCorp): Corp
    {
        $corp = $this->corpRepository->findOneBy([
            'corpId' => $authCorp->getCorpId(),
        ]);

        if (null === $corp) {
            $corp = new Corp();
            $corpId = $authCorp->getCorpId();
            if (null !== $corpId) {
                $corp->setCorpId($corpId);
            }
        }

        $corp->setFromProvider(true);

        $corpName = $authCorp->getCorpName();
        if (null !== $corpName) {
            $corp->setName($corpName);
        }

        $this->entityManager->persist($corp);
        $this->entityManager->flush();

        return $corp;
    }

    private function syncAgents(AuthCorp $authCorp, Corp $corp): void
    {
        $authInfo = $authCorp->getAuthInfo();

        $agents = $authInfo['agent'] ?? $authInfo;
        if (!is_array($agents)) {
            return;
        }

        foreach ($agents as $item) {
            $this->syncAgent($item, $corp, $authCorp);
        }
    }

    /**
     * @param mixed $item
     */
    private function syncAgent($item, Corp $corp, AuthCorp $authCorp): void
    {
        if (!is_array($item) || !isset($item['agentid'], $item['name'])) {
            return;
        }

        $agentId = $item['agentid'];
        if (!is_string($agentId)) {
            return;
        }

        $agent = $this->agentRepository->findOneBy([
            'corp' => $corp,
            'agentId' => $agentId,
        ]);

        if (null === $agent) {
            $agent = new Agent();
            $agent->setCorp($corp);
            $agent->setAgentId($agentId);
        }

        // 确保数组键都是字符串类型
        $normalizedItem = [];
        foreach ($item as $key => $value) {
            if (is_string($key)) {
                $normalizedItem[$key] = $value;
            }
        }

        /** @var array<string, mixed> $normalizedItem */
        $this->populateAgentData($agent, $normalizedItem, $authCorp);

        $this->entityManager->persist($agent);
        $this->entityManager->flush();
    }

    /**
     * @param array<string, mixed> $item
     */
    private function populateAgentData(Agent $agent, array $item, AuthCorp $authCorp): void
    {
        $name = $item['name'];
        if (is_string($name)) {
            $agent->setName($name);
        }

        $squareLogoUrl = $item['square_logo_url'] ?? null;
        if (is_string($squareLogoUrl)) {
            $agent->setSquareLogoUrl($squareLogoUrl);
        }

        $agent->setAccessToken($authCorp->getAccessToken());
        $agent->setAccessTokenExpireTime($authCorp->getTokenExpireTime());
    }

    protected function getRequestMethod(RequestInterface $request): string
    {
        return $request->getRequestMethod() ?? 'POST';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRequestOptions(RequestInterface $request): array
    {
        /** @var array<string, mixed> $options */
        $options = $request->getRequestOptions();
        if (!is_array($options)) {
            $options = [];
        }
        if (!isset($options['query']) || !is_array($options['query'])) {
            $options['query'] = [];
        }

        if ($request instanceof WithSuiteRequest) {
            $options['query']['suite_access_token'] = $this->getTokenManager()->ensureSuiteAccessToken($request->getSuite());
        }

        if ($request instanceof WithAuthCorpRequest) {
            $options['query']['access_token'] = $this->getTokenManager()->ensureAuthCorpAccessToken($request->getAuthCorp());
        }

        if ($request instanceof WithProviderRequest) {
            $options['query']['provider_access_token'] = $this->getTokenManager()->ensureProviderAccessToken($request->getProvider());
        }

        return $options;
    }

    protected function formatResponse(RequestInterface $request, ResponseInterface $response): mixed
    {
        $json = $response->getContent();
        $json = Json::decode($json);

        if (is_array($json) && isset($json['errcode'])) {
            $errcode = $json['errcode'];
            if (is_int($errcode) && 0 !== $errcode) {
                $errmsg = $json['errmsg'] ?? '未知错误';
                $errorMessage = is_string($errmsg) ? $errmsg : '未知错误';
                throw new GeneralHttpClientException($request, $response, $errorMessage, $errcode);
            }
        }

        return $json;
    }
}
