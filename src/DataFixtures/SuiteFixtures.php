<?php

namespace WechatWorkProviderBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatWorkProviderBundle\Entity\Provider;
use WechatWorkProviderBundle\Entity\Suite;

#[When(env: 'test')]
#[When(env: 'dev')]
class SuiteFixtures extends Fixture implements DependentFixtureInterface
{
    public const SUITE_MAIN = 'suite-main';
    public const SUITE_BACKUP = 'suite-backup';

    public function load(ObjectManager $manager): void
    {
        $suite1 = new Suite();
        $suite1->setSuiteId('dk123456789abcdef');
        $suite1->setSuiteSecret('suite_secret_123456789');
        $suite1->setSuiteTicket('suite_ticket_abcdefghijk');
        $suite1->setToken('suite_callback_token_123');
        $suite1->setEncodingAesKey('suite_encoding_aes_key_abcdefghijklmnopqrstuvwxyz');
        $suite1->setProvider($this->getReference(ProviderFixtures::PROVIDER_MAIN, Provider::class));

        $suite2 = new Suite();
        $suite2->setSuiteId('dk987654321fedcba');
        $suite2->setSuiteSecret('suite_secret_987654321');
        $suite2->setSuiteTicket('suite_ticket_zyxwvutsrqp');
        $suite2->setToken('suite_callback_token_456');
        $suite2->setEncodingAesKey('suite_encoding_aes_key_zyxwvutsrqponmlkjihgfedcba');
        $suite2->setProvider($this->getReference(ProviderFixtures::PROVIDER_MAIN, Provider::class));

        $manager->persist($suite1);
        $manager->persist($suite2);

        $this->addReference(self::SUITE_MAIN, $suite1);
        $this->addReference(self::SUITE_BACKUP, $suite2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProviderFixtures::class,
        ];
    }
}
