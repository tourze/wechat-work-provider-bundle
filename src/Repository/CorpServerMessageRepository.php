<?php

namespace WechatWorkProviderBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatWorkProviderBundle\Entity\CorpServerMessage;

/**
 * @extends ServiceEntityRepository<CorpServerMessage>
 */
#[AsRepository(entityClass: CorpServerMessage::class)]
class CorpServerMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CorpServerMessage::class);
    }

    public function save(CorpServerMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CorpServerMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
