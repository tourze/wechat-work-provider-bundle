<?php

namespace WechatWorkProviderBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatWorkProviderBundle\Entity\ProviderServerMessage;

/**
 * @extends ServiceEntityRepository<ProviderServerMessage>
 */
#[AsRepository(entityClass: ProviderServerMessage::class)]
class ProviderServerMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProviderServerMessage::class);
    }

    public function save(ProviderServerMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProviderServerMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
