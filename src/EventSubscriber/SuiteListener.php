<?php

namespace WechatWorkProviderBundle\EventSubscriber;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use WechatWorkProviderBundle\Entity\SuiteServerMessage;
use WechatWorkProviderBundle\Repository\SuiteRepository;

#[AsEntityListener(event: Events::postPersist, method: 'updateSuiteTicket', entity: SuiteServerMessage::class)]
class SuiteListener
{
    public function __construct(
        private readonly SuiteRepository $suiteRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 收到服务端回调消息时，更新推送过来的Ticket，存储SuiteTicket
     */
    public function updateSuiteTicket(SuiteServerMessage $message): void
    {
        $msg = $message->getContext();
        $InfoType = $msg['InfoType'] ?? null;

        if ('suite_ticket' === $InfoType) {
            $suite = $this->suiteRepository->findOneBy([
                'suiteId' => $msg['SuiteId'],
            ]);
            if ($suite !== null) {
                $suite->setSuiteTicket($msg['SuiteTicket']);
                $suite->setTicketExpireTime(CarbonImmutable::now()->addMinutes(30));
                $this->entityManager->persist($suite);
                $this->entityManager->flush();
            }
        }
    }
}
