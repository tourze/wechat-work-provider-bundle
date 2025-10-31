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
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use WechatWorkProviderBundle\Entity\Provider;

/**
 * 服务商管理控制器
 *
 * @extends AbstractCrudController<Provider>
 */
#[AdminCrud(routePath: '/wechat-work-provider/provider', routeName: 'wechat_work_provider_provider')]
final class ProviderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Provider::class;
    }

    /**
     * 配置CRUD基本设置
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('服务商')
            ->setEntityLabelInPlural('服务商')
            ->setPageTitle('index', '服务商管理')
            ->setPageTitle('new', '新建服务商')
            ->setPageTitle('detail', '服务商详情')
            ->setPageTitle('edit', '编辑服务商')
            ->setPaginatorPageSize(20)
            ->setDefaultSort(['createTime' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('corpId', '服务商企业ID'))
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

            TextField::new('corpId', '服务商企业ID')
                ->setRequired(true)
                ->setHelp('服务商的企业微信corpId')
                ->setMaxLength(64),

            TextField::new('providerSecret', '服务商Secret')
                ->setRequired(true)
                ->setHelp('服务商的secret，在服务商管理后台可见')
                ->setMaxLength(200)
                ->hideOnIndex(),

            TextField::new('providerAccessToken', '服务商AccessToken')
                ->setRequired(false)
                ->setHelp('服务商当前有效的AccessToken')
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
                ->setMaxLength(128)
                ->hideOnIndex(),

            AssociationField::new('suites', '关联应用模板')
                ->setRequired(false)
                ->setHelp('该服务商下的应用模板列表')
                ->hideOnForm(),

            AssociationField::new('serverMessages', '服务器消息')
                ->setRequired(false)
                ->setHelp('该服务商的服务器回调消息记录')
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
