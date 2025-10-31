<?php

namespace WechatWorkProviderBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatWorkProviderBundle\Entity\AuthCorp;
use WechatWorkProviderBundle\Entity\Suite;

#[When(env: 'test')]
#[When(env: 'dev')]
class AuthCorpFixtures extends Fixture implements DependentFixtureInterface
{
    public const AUTH_CORP_TEST = 'auth-corp-test';
    public const AUTH_CORP_DEMO = 'auth-corp-demo';

    public function load(ObjectManager $manager): void
    {
        $authCorp1 = new AuthCorp();
        $authCorp1->setCorpId('ww1234567890abcdef');
        $authCorp1->setCorpName('测试企业微信公司');
        $authCorp1->setCorpType('verified');
        $authCorp1->setCorpUserMax(500);
        $authCorp1->setCorpFullName('测试企业微信有限公司');
        $authCorp1->setSubjectType('enterprise');
        $authCorp1->setCorpScale('50-200人');
        $authCorp1->setCorpIndustry('IT服务');
        $authCorp1->setCorpSubIndustry('软件开发');
        $authCorp1->setPermanentCode('permanent_code_123456');
        $authCorp1->setSuite($this->getReference(SuiteFixtures::SUITE_MAIN, Suite::class));

        $authCorp2 = new AuthCorp();
        $authCorp2->setCorpId('ww0987654321fedcba');
        $authCorp2->setCorpName('示例科技公司');
        $authCorp2->setCorpType('verified');
        $authCorp2->setCorpUserMax(1000);
        $authCorp2->setCorpFullName('示例科技有限责任公司');
        $authCorp2->setSubjectType('enterprise');
        $authCorp2->setCorpScale('200-500人');
        $authCorp2->setCorpIndustry('互联网');
        $authCorp2->setCorpSubIndustry('电子商务');
        $authCorp2->setPermanentCode('permanent_code_789012');
        $authCorp2->setSuite($this->getReference(SuiteFixtures::SUITE_MAIN, Suite::class));

        $manager->persist($authCorp1);
        $manager->persist($authCorp2);

        $this->addReference(self::AUTH_CORP_TEST, $authCorp1);
        $this->addReference(self::AUTH_CORP_DEMO, $authCorp2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SuiteFixtures::class,
        ];
    }
}
