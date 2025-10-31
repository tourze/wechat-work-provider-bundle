<?php

declare(strict_types=1);

namespace WechatWorkProviderBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use WechatWorkProviderBundle\Entity\ProviderServerMessage;

/**
 * 服务商服务器消息管理控制器
 *
 * @extends AbstractCrudController<ProviderServerMessage>
 */
#[AdminCrud(routePath: '/wechat-work-provider/provider-server-message', routeName: 'wechat_work_provider_provider_server_message')]
final class ProviderServerMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProviderServerMessage::class;
    }

    /**
     * 配置CRUD基本设置
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('服务商服务器消息')
            ->setEntityLabelInPlural('服务商服务器消息')
            ->setPageTitle('index', '服务商服务器消息管理')
            ->setPageTitle('new', '新建服务商服务器消息')
            ->setPageTitle('detail', '服务商服务器消息详情')
            ->setPageTitle('edit', '编辑服务商服务器消息')
            ->setPaginatorPageSize(20)
            ->setDefaultSort(['createTime' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('provider', '关联服务商'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            AssociationField::new('provider', '关联服务商')
                ->setRequired(false)
                ->setHelp('消息关联的服务商'),

            CodeEditorField::new('context', '上下文数据')
                ->setRequired(false)
                ->setHelp('回调消息的上下文JSON数据')
                ->setLanguage('javascript')
                ->hideOnIndex(),

            TextareaField::new('rawData', '原始数据')
                ->setRequired(false)
                ->setHelp('回调的原始数据文本')
                ->setMaxLength(65535)
                ->hideOnIndex(),

            DateTimeField::new('createTime', '创建时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),
        ];
    }
}
