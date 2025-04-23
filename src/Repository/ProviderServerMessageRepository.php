<?php

namespace WechatWorkProviderBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatWorkProviderBundle\Entity\ProviderServerMessage;

/**
 * @method ProviderServerMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProviderServerMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProviderServerMessage[]    findAll()
 * @method ProviderServerMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderServerMessageRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProviderServerMessage::class);
    }
}
