<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use WechatWorkProviderBundle\Entity\CorpServerMessage;

/**
 * 企业服务器消息管理控制器
 *
 * @extends AbstractCrudController<CorpServerMessage>
 */
#[AdminCrud(routePath: '/wechat-work-provider/corp-server-message', routeName: 'wechat_work_provider_corp_server_message')]
final class CorpServerMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CorpServerMessage::class;
    }

    /**
     * 配置CRUD基本设置
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('企业服务器消息')
            ->setEntityLabelInPlural('企业服务器消息')
            ->setPageTitle('index', '企业服务器消息管理')
            ->setPageTitle('new', '新建企业服务器消息')
            ->setPageTitle('detail', '企业服务器消息详情')
            ->setPageTitle('edit', '编辑企业服务器消息')
            ->setPaginatorPageSize(20)
            ->setDefaultSort(['createTime' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('toUserName', '接收方企业ID'))
            ->add(TextFilter::new('fromUserName', '发送方用户ID'))
            ->add(EntityFilter::new('authCorp', '关联授权企业'))
            ->add(NumericFilter::new('createTime', '消息创建时间'))
            ->add(TextFilter::new('msgType', '消息类型'))
            ->add(TextFilter::new('event', '事件类型'))
            ->add(TextFilter::new('changeType', '变更类型'))
            ->add(TextFilter::new('chatId', '群聊ID'))
            ->add(TextFilter::new('externalUserId', '外部联系人ID'))
            ->add(NumericFilter::new('joinScene', '加入场景'))
            ->add(NumericFilter::new('memChangeCnt', '成员变更数量'))
            ->add(NumericFilter::new('quitScene', '退出场景'))
            ->add(TextFilter::new('state', '状态'))
            ->add(TextFilter::new('updateDetail', '更新详情'))
            ->add(TextFilter::new('userId', '用户ID'))
            ->add(TextFilter::new('welcomeCode', '欢迎语Code'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            TextField::new('toUserName', '接收方企业ID')
                ->setRequired(true)
                ->setHelp('企业微信CorpID')
                ->setMaxLength(64),

            TextField::new('fromUserName', '发送方用户ID')
                ->setRequired(false)
                ->setHelp('成员UserID，可为空')
                ->setMaxLength(128),

            AssociationField::new('authCorp', '关联授权企业')
                ->setRequired(false)
                ->setHelp('消息关联的授权企业'),

            IntegerField::new('createTime', '消息创建时间')
                ->setRequired(false)
                ->setHelp('消息创建时间戳')
                ->setFormTypeOptions(['attr' => ['min' => 0]]),

            CodeEditorField::new('decryptData', '解密后数据')
                ->setRequired(false)
                ->setHelp('Encrypt参数解密后的内容JSON数据')
                ->setLanguage('javascript')
                ->hideOnIndex(),

            CodeEditorField::new('rawData', '原始数据')
                ->setRequired(false)
                ->setHelp('回调的原始数据JSON')
                ->setLanguage('javascript')
                ->hideOnIndex(),

            TextField::new('msgType', '消息类型')
                ->setRequired(false)
                ->setHelp('消息类型标识')
                ->setMaxLength(50),

            TextField::new('event', '事件类型')
                ->setRequired(false)
                ->setHelp('事件类型标识')
                ->setMaxLength(120),

            TextField::new('changeType', '变更类型')
                ->setRequired(false)
                ->setHelp('变更事件的具体类型')
                ->setMaxLength(120)
                ->hideOnIndex(),

            TextField::new('chatId', '群聊ID')
                ->setRequired(false)
                ->setHelp('群聊相关消息的群聊ID')
                ->setMaxLength(120)
                ->hideOnIndex(),

            TextField::new('externalUserId', '外部联系人ID')
                ->setRequired(false)
                ->setHelp('外部联系人相关消息的用户ID')
                ->setMaxLength(120)
                ->hideOnIndex(),

            IntegerField::new('joinScene', '加入场景')
                ->setRequired(false)
                ->setHelp('成员加入的场景值')
                ->setFormTypeOptions(['attr' => ['min' => 0]])
                ->hideOnIndex(),

            IntegerField::new('memChangeCnt', '成员变更数量')
                ->setRequired(false)
                ->setHelp('成员变更的数量')
                ->setFormTypeOptions(['attr' => ['min' => 0]])
                ->hideOnIndex(),

            IntegerField::new('quitScene', '退出场景')
                ->setRequired(false)
                ->setHelp('成员退出的场景值')
                ->setFormTypeOptions(['attr' => ['min' => 0]])
                ->hideOnIndex(),

            TextField::new('state', '状态')
                ->setRequired(false)
                ->setHelp('消息状态标识')
                ->setMaxLength(120)
                ->hideOnIndex(),

            TextField::new('updateDetail', '更新详情')
                ->setRequired(false)
                ->setHelp('更新事件的详细信息')
                ->setMaxLength(120)
                ->hideOnIndex(),

            TextField::new('userId', '用户ID')
                ->setRequired(false)
                ->setHelp('相关的企业内部用户ID')
                ->setMaxLength(120),

            TextField::new('welcomeCode', '欢迎语Code')
                ->setRequired(false)
                ->setHelp('欢迎语相关的Code')
                ->setMaxLength(140)
                ->hideOnIndex(),

            CodeEditorField::new('response', '响应数据')
                ->setRequired(false)
                ->setHelp('回调处理的响应数据JSON')
                ->setLanguage('javascript')
                ->hideOnIndex(),
        ];
    }
}
