<?php

namespace WechatWorkProviderBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatWorkProviderBundle\Entity\SuiteServerMessage;

/**
 * @method SuiteServerMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuiteServerMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuiteServerMessage[]    findAll()
 * @method SuiteServerMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuiteServerMessageRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuiteServerMessage::class);
    }
}
