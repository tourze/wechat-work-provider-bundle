<?php

namespace WechatWorkProviderBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkProviderBundle\Entity\Suite;

/**
 * @method Suite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Suite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Suite[]    findAll()
 * @method Suite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Suite::class);
    }
}
