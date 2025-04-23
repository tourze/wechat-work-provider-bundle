<?php

namespace WechatWorkProviderBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkProviderBundle\Entity\CorpServerMessage;

/**
 * @method CorpServerMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method CorpServerMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method CorpServerMessage[]    findAll()
 * @method CorpServerMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CorpServerMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CorpServerMessage::class);
    }
}
