<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use WechatWorkProviderBundle\Entity\Suite;

/**
 * 应用模板管理控制器
 *
 * @extends AbstractCrudController<Suite>
 */
#[AdminCrud(routePath: '/wechat-work-provider/suite', routeName: 'wechat_work_provider_suite')]
final class SuiteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Suite::class;
    }

    /**
     * 配置CRUD基本设置
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('应用模板')
            ->setEntityLabelInPlural('应用模板')
            ->setPageTitle('index', '应用模板管理')
            ->setPageTitle('new', '新建应用模板')
            ->setPageTitle('detail', '应用模板详情')
            ->setPageTitle('edit', '编辑应用模板')
            ->setPaginatorPageSize(20)
            ->setDefaultSort(['createTime' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('provider', '关联服务商'))
            ->add(TextFilter::new('suiteId', '模板ID'))
            ->add(DateTimeFilter::new('tokenExpireTime', 'Token过期时间'))
            ->add(DateTimeFilter::new('ticketExpireTime', 'Ticket过期时间'))
            ->add(TextFilter::new('token', '回调Token'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            AssociationField::new('provider', '关联服务商')
                ->setRequired(true)
                ->setHelp('应用模板所属的服务商'),

            TextField::new('suiteId', '模板ID')
                ->setRequired(true)
                ->setHelp('第三方应用id或代开发应用模板id')
                ->setMaxLength(64),

            TextField::new('suiteSecret', '模板Secret')
                ->setRequired(true)
                ->setHelp('应用模板的Secret密钥')
                ->setMaxLength(200)
                ->hideOnIndex(),

            TextField::new('suiteTicket', '模板Ticket')
                ->setRequired(false)
                ->setHelp('企业微信推送的ticket')
                ->setMaxLength(250)
                ->hideOnIndex(),

            TextField::new('suiteAccessToken', 'AccessToken')
                ->setRequired(false)
                ->setHelp('应用模板的AccessToken')
                ->setMaxLength(200)
                ->hideOnIndex(),

            DateTimeField::new('tokenExpireTime', 'Token过期时间')
                ->setRequired(false)
                ->setHelp('AccessToken的过期时间')
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->hideOnIndex(),

            DateTimeField::new('ticketExpireTime', 'Ticket过期时间')
                ->setRequired(false)
                ->setHelp('Ticket的过期时间')
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->hideOnIndex(),

            TextField::new('token', '回调Token')
                ->setRequired(false)
                ->setHelp('回调验证用的Token')
                ->setMaxLength(40)
                ->hideOnIndex(),

            TextField::new('encodingAesKey', '回调加密密钥')
                ->setRequired(false)
                ->setHelp('回调消息加密用的EncodingAESKey')
                ->setMaxLength(120)
                ->hideOnIndex(),

            AssociationField::new('authCorps', '授权企业')
                ->setRequired(false)
                ->setHelp('使用该应用模板的授权企业列表')
                ->hideOnForm(),

            AssociationField::new('serverMessages', '服务器消息')
                ->setRequired(false)
                ->setHelp('该应用模板的服务器回调消息记录')
                ->hideOnForm(),

            DateTimeField::new('createTime', '创建时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),

            DateTimeField::new('updateTime', '更新时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),
        ];
    }
}
