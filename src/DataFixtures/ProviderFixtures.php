<?php

namespace WechatWorkProviderBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatWorkProviderBundle\Entity\Provider;

#[When(env: 'test')]
#[When(env: 'dev')]
class ProviderFixtures extends Fixture
{
    public const PROVIDER_MAIN = 'provider-main';
    public const PROVIDER_BACKUP = 'provider-backup';

    public function load(ObjectManager $manager): void
    {
        $provider1 = new Provider();
        $provider1->setCorpId('ww_provider_123456789');
        $provider1->setProviderSecret('provider_secret_abcdefghijk');
        $provider1->setToken('callback_token_123');
        $provider1->setEncodingAesKey('encoding_aes_key_abcdefghijklmnopqrstuvwxyz123456');

        $provider2 = new Provider();
        $provider2->setCorpId('ww_provider_987654321');
        $provider2->setProviderSecret('provider_secret_zyxwvutsrqp');
        $provider2->setToken('callback_token_456');
        $provider2->setEncodingAesKey('encoding_aes_key_654321zyxwvutsrqponmlkjihgfedcba');

        $manager->persist($provider1);
        $manager->persist($provider2);

        $this->addReference(self::PROVIDER_MAIN, $provider1);
        $this->addReference(self::PROVIDER_BACKUP, $provider2);

        $manager->flush();
    }
}
