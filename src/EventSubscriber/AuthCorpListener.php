<?php

namespace WechatWorkProviderBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Entity\Suite;
use WechatWorkProviderBundle\Entity\SuiteServerMessage;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;
use WechatWorkProviderBundle\Repository\SuiteRepository;
use WechatWorkProviderBundle\Request\GetPermanentCodeRequest;
use WechatWorkProviderBundle\Service\ProviderService;

#[AsEntityListener(event: Events::postPersist, method: 'autoCreateAuthCorp', entity: SuiteServerMessage::class)]
class AuthCorpListener
{
    public function __construct(
        private readonly SuiteRepository $suiteRepository,
        private readonly ProviderService $providerService,
        private readonly AuthCorpRepository $authCorpRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 收到服务端回调消息时，自动创建授权人信息
     */
    public function autoCreateAuthCorp(SuiteServerMessage $message): void
    {
        $msg = $message->getContext();
        if (!is_array($msg)) {
            return;
        }
        $InfoType = $msg['InfoType'] ?? null;
        if (!is_string($InfoType)) {
            return;
        }

        if (!$this->isAuthEvent($InfoType)) {
            return;
        }

        /** @var array<string, mixed> $msg */
        $suite = $this->validateAndGetSuite($msg);
        if (null === $suite) {
            return;
        }

        $response = $this->getPermanentCodeResponse($msg, $suite);
        if (null === $response) {
            return;
        }

        $authCorpInfo = $this->validateAndGetAuthCorpInfo($response);
        if (null === $authCorpInfo) {
            return;
        }

        $corpId = $authCorpInfo['corpid'];
        if (!is_string($corpId)) {
            return;
        }
        $authCorp = $this->findOrCreateAuthCorp($suite, $corpId);
        $this->populateAuthCorpFromResponse($authCorp, $authCorpInfo, $response);

        $this->entityManager->persist($authCorp);
        $this->entityManager->flush();
        $this->providerService->syncAuthCorpToCorpAndAgent($authCorp);
    }

    private function isAuthEvent(string $infoType): bool
    {
        return 'create_auth' === $infoType || 'reset_permanent_code' === $infoType;
    }

    /**
     * @param array<string, mixed> $msg
     */
    private function validateAndGetSuite(array $msg): ?Suite
    {
        if (!isset($msg['SuiteId']) || !isset($msg['AuthCode'])) {
            return null;
        }

        return $this->suiteRepository->findOneBy([
            'suiteId' => $msg['SuiteId'],
        ]);
    }

    /**
     * @param array<string, mixed> $msg
     * @return array<string, mixed>|null
     */
    private function getPermanentCodeResponse(array $msg, Suite $suite): ?array
    {
        $apiRequest = new GetPermanentCodeRequest();
        $authCode = $msg['AuthCode'];
        if (!is_string($authCode)) {
            return null;
        }
        $apiRequest->setAuthCode($authCode);
        $apiRequest->setSuite($suite);

        $response = $this->providerService->request($apiRequest);

        if (!is_array($response)) {
            return null;
        }

        /** @var array<string, mixed> $response */

        return $response;
    }

    /**
     * @param array<string, mixed> $response
     * @return array<string, mixed>|null
     */
    private function validateAndGetAuthCorpInfo(array $response): ?array
    {
        if (!isset($response['auth_corp_info']) || !is_array($response['auth_corp_info'])) {
            return null;
        }

        $authCorpInfo = $response['auth_corp_info'];
        if (!isset($authCorpInfo['corpid']) || !is_string($authCorpInfo['corpid'])) {
            return null;
        }

        // 确保返回的数组符合 array<string, mixed> 类型
        $result = [];
        foreach ($authCorpInfo as $key => $value) {
            if (is_string($key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    private function findOrCreateAuthCorp(Suite $suite, string $corpId): AuthCorp
    {
        $authCorp = $this->authCorpRepository->findOneBy([
            'suite' => $suite,
            'corpId' => $corpId,
        ]);

        if (null === $authCorp) {
            $authCorp = new AuthCorp();
            $authCorp->setSuite($suite);
            $authCorp->setCorpId($corpId);
        }

        return $authCorp;
    }

    /**
     * @param array<string, mixed> $authCorpInfo
     * @param array<string, mixed> $response
     */
    private function populateAuthCorpFromResponse(AuthCorp $authCorp, array $authCorpInfo, array $response): void
    {
        // 设置基础信息
        $authCorp->setPermanentCode($this->getStringValue($response, 'permanent_code'));

        // 设置企业信息
        $corpName = $this->getStringValue($authCorpInfo, 'corp_name', '');
        if (null !== $corpName) {
            $authCorp->setCorpName($corpName);
        }
        $authCorp->setCorpType($this->getStringValue($authCorpInfo, 'corp_type'));
        $authCorp->setCorpSquareLogoUrl($this->getStringValue($authCorpInfo, 'corp_square_logo_url'));
        $authCorp->setCorpFullName($this->getStringValue($authCorpInfo, 'corp_full_name'));
        $authCorp->setCorpScale($this->getStringValue($authCorpInfo, 'corp_scale'));
        $authCorp->setCorpIndustry($this->getStringValue($authCorpInfo, 'corp_industry'));
        $authCorp->setCorpSubIndustry($this->getStringValue($authCorpInfo, 'corp_sub_industry'));

        // 设置数值信息
        $authCorp->setCorpUserMax($this->getIntValue($authCorpInfo, 'corp_user_max'));
        $subjectType = $this->getIntValue($authCorpInfo, 'subject_type');
        if (null !== $subjectType) {
            $authCorp->setSubjectType((string) $subjectType);
        }

        // 设置数组信息
        $authCorp->setAuthInfo($this->getArrayValue($response, 'auth_info'));
        $authCorp->setAuthUserInfo($this->getArrayValue($response, 'auth_user_info'));
        $authCorp->setDealerCorpInfo($this->getArrayValue($response, 'dealer_corp_info'));
        $authCorp->setRegisterCodeInfo($this->getArrayValue($response, 'register_code_info'));

        // 设置状态信息
        $authCorp->setState($this->getStringValue($response, 'state'));
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getStringValue(array $data, string $key, ?string $default = null): ?string
    {
        $value = $data[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getIntValue(array $data, string $key): ?int
    {
        $value = $data[$key] ?? null;

        return is_int($value) ? $value : null;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    private function getArrayValue(array $data, string $key): ?array
    {
        $value = $data[$key] ?? null;
        if (!is_array($value)) {
            return null;
        }

        // 确保返回的数组符合 array<string, mixed> 类型
        $result = [];
        foreach ($value as $k => $v) {
            if (is_string($k)) {
                $result[$k] = $v;
            }
        }

        return $result;
    }
}
