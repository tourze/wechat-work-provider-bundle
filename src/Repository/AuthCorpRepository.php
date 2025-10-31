<?php

namespace WechatWorkProviderBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkProviderBundle\Entity\AuthCorp;

/**
 * @extends ServiceEntityRepository<AuthCorp>
 */
#[Autoconfigure(public: true)]
#[AsRepository(entityClass: AuthCorp::class)]
class AuthCorpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly CorpRepository $corpRepository)
    {
        parent::__construct($registry, AuthCorp::class);
    }

    public function save(AuthCorp $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AuthCorp $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAgentByAuthCorp(AuthCorp $authCorp): ?AgentInterface
    {
        $corp = $this->corpRepository->findOneBy([
            'corpId' => $authCorp->getCorpId(),
        ]);
        if (null === $corp) {
            return null;
        }

        $agent = $corp->getAgents()->first();

        return false !== $agent ? $agent : null;
    }
}
