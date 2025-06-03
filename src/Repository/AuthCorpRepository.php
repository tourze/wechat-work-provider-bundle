<?php

namespace WechatWorkProviderBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkProviderBundle\Entity\AuthCorp;

/**
 * @method AuthCorp|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthCorp|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthCorp[]    findAll()
 * @method AuthCorp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthCorpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly CorpRepository $corpRepository)
    {
        parent::__construct($registry, AuthCorp::class);
    }

    public function getAgentByAuthCorp(AuthCorp $authCorp): ?AgentInterface
    {
        $corp = $this->corpRepository->findOneBy([
            'corpId' => $authCorp->getCorpId(),
        ]);
        if (!$corp) {
            return null;
        }

        return $corp->getAgents()->first();
    }
}
