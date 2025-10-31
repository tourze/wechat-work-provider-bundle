<?php

namespace WechatWorkProviderBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatWorkProviderBundle\Entity\Suite;
use WechatWorkProviderBundle\Entity\SuiteServerMessage;

#[When(env: 'test')]
#[When(env: 'dev')]
class SuiteServerMessageFixtures extends Fixture implements DependentFixtureInterface
{
    public const SUITE_MESSAGE_CHANGE_AUTH = 'suite-message-change-auth';
    public const SUITE_MESSAGE_CANCEL_AUTH = 'suite-message-cancel-auth';

    public function load(ObjectManager $manager): void
    {
        $message1 = new SuiteServerMessage();
        $message1->setContext(['type' => 'change_auth', 'suiteId' => 'dk123456789']);
        $message1->setRawData('{"InfoType":"change_auth","SuiteId":"dk123456789","AuthCorpId":"ww123"}');
        $message1->setSuite($this->getReference(SuiteFixtures::SUITE_MAIN, Suite::class));

        $message2 = new SuiteServerMessage();
        $message2->setContext(['type' => 'cancel_auth', 'suiteId' => 'dk123456789']);
        $message2->setRawData('{"InfoType":"cancel_auth","SuiteId":"dk123456789","AuthCorpId":"ww456"}');
        $message2->setSuite($this->getReference(SuiteFixtures::SUITE_MAIN, Suite::class));

        $manager->persist($message1);
        $manager->persist($message2);

        $this->addReference(self::SUITE_MESSAGE_CHANGE_AUTH, $message1);
        $this->addReference(self::SUITE_MESSAGE_CANCEL_AUTH, $message2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SuiteFixtures::class,
        ];
    }
}
