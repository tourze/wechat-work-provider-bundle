<?php

namespace WechatWorkProviderBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Entity\SuiteServerMessage;
use WechatWorkProviderBundle\Repository\AuthCorpRepository;
use WechatWorkProviderBundle\Repository\SuiteRepository;
use WechatWorkProviderBundle\Request\GetPermanentCodeRequest;
use WechatWorkProviderBundle\Service\ProviderService;
use Yiisoft\Arrays\ArrayHelper;

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
        $InfoType = ArrayHelper::getValue($msg, 'InfoType');

        // 如果是授权接入 & 重置永久授权码
        if ('create_auth' === $InfoType || 'reset_permanent_code' === $InfoType) {
            $suite = $this->suiteRepository->findOneBy([
                'suiteId' => $msg['SuiteId'],
            ]);
            $apiRequest = new GetPermanentCodeRequest();
            $apiRequest->setAuthCode($msg['AuthCode']);
            $apiRequest->setSuite($suite);
            // 获取企业永久授权码
            $response = $this->providerService->request($apiRequest);

            $authCorp = $this->authCorpRepository->findOneBy([
                'suite' => $suite,
                'corpId' => $response['auth_corp_info']['corpid'],
            ]);
            if (!$authCorp) {
                $authCorp = new AuthCorp();
                $authCorp->setSuite($suite);
                $authCorp->setCorpId($response['auth_corp_info']['corpid']);
            }
            $authCorp->setPermanentCode($response['permanent_code']);
            $authCorp->setCorpName($response['auth_corp_info']['corp_name'] ?? null);
            $authCorp->setCorpType($response['auth_corp_info']['corp_type'] ?? null);
            $authCorp->setCorpSquareLogoUrl($response['auth_corp_info']['corp_square_logo_url'] ?? null);
            $authCorp->setCorpUserMax($response['auth_corp_info']['corp_user_max'] ?? null);
            $authCorp->setCorpFullName($response['auth_corp_info']['corp_full_name'] ?? null);
            $authCorp->setSubjectType($response['auth_corp_info']['subject_type'] ?? null);
            // $authCorp->setVerifiedEndTime($response['auth_corp_info']['verified_end_time'] ? Carbon::createFromTimestamp($response['auth_corp_info']['verified_end_time']) : null);
            $authCorp->setCorpScale($response['auth_corp_info']['corp_scale'] ?? null);
            $authCorp->setCorpIndustry($response['auth_corp_info']['corp_industry'] ?? null);
            $authCorp->setCorpSubIndustry($response['auth_corp_info']['corp_sub_industry'] ?? null);

            $authCorp->setAuthInfo($response['auth_info'] ?? null);
            $authCorp->setAuthUserInfo($response['auth_user_info'] ?? null);
            $authCorp->setDealerCorpInfo($response['dealer_corp_info'] ?? null);
            $authCorp->setRegisterCodeInfo($response['register_code_info'] ?? null);
            $authCorp->setState($response['state'] ?? null);

            $this->entityManager->persist($authCorp);
            $this->entityManager->flush();
            $this->providerService->syncAuthCorpToCorpAndAgent($authCorp);
        }
    }
}
