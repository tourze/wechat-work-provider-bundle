<?php

namespace WechatWorkProviderBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Entity\CorpServerMessage;

#[When(env: 'test')]
#[When(env: 'dev')]
class CorpServerMessageFixtures extends Fixture implements DependentFixtureInterface
{
    public const CORP_MESSAGE_CONTACT = 'corp-message-contact';
    public const CORP_MESSAGE_CHAT = 'corp-message-chat';

    public function load(ObjectManager $manager): void
    {
        $message1 = new CorpServerMessage();
        $message1->setToUserName('ww1234567890abcdef');
        $message1->setFromUserName('userid123');
        $message1->setCreateTime(time());
        $message1->setMsgType('event');
        $message1->setEvent('change_external_contact');
        $message1->setChangeType('add_external_contact');
        $message1->setUserId('userid123');
        $message1->setExternalUserId('external123');
        $message1->setJoinScene(1);
        $message1->setAuthCorp($this->getReference(AuthCorpFixtures::AUTH_CORP_TEST, AuthCorp::class));

        $message2 = new CorpServerMessage();
        $message2->setToUserName('ww1234567890abcdef');
        $message2->setFromUserName('userid456');
        $message2->setCreateTime(time() - 3600);
        $message2->setMsgType('event');
        $message2->setEvent('change_external_chat');
        $message2->setChangeType('update_chat');
        $message2->setChatId('chat123456');
        $message2->setMemChangeCnt(2);
        $message2->setAuthCorp($this->getReference(AuthCorpFixtures::AUTH_CORP_TEST, AuthCorp::class));

        $manager->persist($message1);
        $manager->persist($message2);

        $this->addReference(self::CORP_MESSAGE_CONTACT, $message1);
        $this->addReference(self::CORP_MESSAGE_CHAT, $message2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AuthCorpFixtures::class,
        ];
    }
}
