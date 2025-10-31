<?php

namespace WechatWorkProviderBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Entity\ProviderServerMessage;

#[When(env: 'test')]
#[When(env: 'dev')]
class ProviderServerMessageFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROVIDER_MESSAGE_TICKET = 'provider-message-ticket';
    public const PROVIDER_MESSAGE_AUTH = 'provider-message-auth';

    public function load(ObjectManager $manager): void
    {
        $message1 = new ProviderServerMessage();
        $message1->setContext(['type' => 'suite_ticket', 'suiteId' => 'dk123456789']);
        $message1->setRawData('{"InfoType":"suite_ticket","SuiteId":"dk123456789","Ticket":"ticket123"}');
        $message1->setProvider($this->getReference(ProviderFixtures::PROVIDER_MAIN, Provider::class));

        $message2 = new ProviderServerMessage();
        $message2->setContext(['type' => 'create_auth', 'authCode' => 'auth123']);
        $message2->setRawData('{"InfoType":"create_auth","AuthCode":"auth123","State":"teststate"}');
        $message2->setProvider($this->getReference(ProviderFixtures::PROVIDER_MAIN, Provider::class));

        $manager->persist($message1);
        $manager->persist($message2);

        $this->addReference(self::PROVIDER_MESSAGE_TICKET, $message1);
        $this->addReference(self::PROVIDER_MESSAGE_AUTH, $message2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProviderFixtures::class,
        ];
    }
}
