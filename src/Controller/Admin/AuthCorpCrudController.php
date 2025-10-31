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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use WechatWorkProviderBundle\Entity\AuthCorp;

/**
 * 授权企业管理控制器
 *
 * @extends AbstractCrudController<AuthCorp>
 */
#[AdminCrud(routePath: '/wechat-work-provider/auth-corp', routeName: 'wechat_work_provider_auth_corp')]
final class AuthCorpCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AuthCorp::class;
    }

    /**
     * 配置CRUD基本设置
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('授权企业')
            ->setEntityLabelInPlural('授权企业')
            ->setPageTitle('index', '授权企业管理')
            ->setPageTitle('new', '新建授权企业')
            ->setPageTitle('detail', '授权企业详情')
            ->setPageTitle('edit', '编辑授权企业')
            ->setPaginatorPageSize(20)
            ->setDefaultSort(['createTime' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('corpId', '企业微信ID'))
            ->add(TextFilter::new('corpName', '企业简称'))
            ->add(TextFilter::new('corpType', '企业类型'))
            ->add(NumericFilter::new('corpUserMax', '用户规模'))
            ->add(TextFilter::new('corpFullName', '企业全称'))
            ->add(TextFilter::new('subjectType', '主体类型'))
            ->add(TextFilter::new('corpScale', '企业规模'))
            ->add(TextFilter::new('corpIndustry', '所属行业'))
            ->add(TextFilter::new('corpSubIndustry', '子行业'))
            ->add(TextFilter::new('state', '状态值'))
            ->add(DateTimeFilter::new('tokenExpireTime', 'Token过期时间'))
            ->add(EntityFilter::new('suite', '关联应用模板'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            TextField::new('corpId', '企业微信ID')
                ->setRequired(true)
                ->setHelp('授权方企业微信corpId')
                ->setMaxLength(80),

            TextField::new('corpName', '企业简称')
                ->setRequired(true)
                ->setHelp('授权方企业简称')
                ->setMaxLength(120),

            TextField::new('corpType', '企业类型')
                ->setRequired(false)
                ->setHelp('授权方企业类型')
                ->setMaxLength(30),

            UrlField::new('corpSquareLogoUrl', '企业方形头像')
                ->setRequired(false)
                ->setHelp('授权方企业方形头像URL')
                ->hideOnIndex(),

            IntegerField::new('corpUserMax', '用户规模')
                ->setRequired(false)
                ->setHelp('授权方企业用户规模')
                ->setFormTypeOptions(['attr' => ['min' => 0]])
                ->hideOnIndex(),

            TextField::new('corpFullName', '企业全称')
                ->setRequired(false)
                ->setHelp('授权方企业全称')
                ->setMaxLength(200)
                ->hideOnIndex(),

            TextField::new('subjectType', '主体类型')
                ->setRequired(false)
                ->setHelp('企业主体类型')
                ->setMaxLength(16)
                ->hideOnIndex(),

            TextField::new('corpScale', '企业规模')
                ->setRequired(false)
                ->setHelp('企业规模分类')
                ->setMaxLength(40)
                ->hideOnIndex(),

            TextField::new('corpIndustry', '所属行业')
                ->setRequired(false)
                ->setHelp('企业所属行业')
                ->setMaxLength(100)
                ->hideOnIndex(),

            TextField::new('corpSubIndustry', '子行业')
                ->setRequired(false)
                ->setHelp('企业所属子行业')
                ->setMaxLength(100)
                ->hideOnIndex(),

            CodeEditorField::new('authInfo', '授权信息')
                ->setRequired(false)
                ->setHelp('授权详细信息JSON数据')
                ->setLanguage('javascript')
                ->hideOnIndex(),

            CodeEditorField::new('authUserInfo', '授权管理员信息')
                ->setRequired(false)
                ->setHelp('授权管理员详细信息JSON数据')
                ->setLanguage('javascript')
                ->hideOnIndex(),

            CodeEditorField::new('dealerCorpInfo', '代理商信息')
                ->setRequired(false)
                ->setHelp('代理服务商企业信息JSON数据')
                ->setLanguage('javascript')
                ->hideOnIndex(),

            CodeEditorField::new('registerCodeInfo', '推广二维码信息')
                ->setRequired(false)
                ->setHelp('推广二维码安装相关信息JSON数据')
                ->setLanguage('javascript')
                ->hideOnIndex(),

            TextField::new('state', '状态值')
                ->setRequired(false)
                ->setHelp('安装应用时授权链接中的state值')
                ->setMaxLength(100)
                ->hideOnIndex(),

            TextField::new('permanentCode', '永久授权码')
                ->setRequired(false)
                ->setHelp('企业微信永久授权码')
                ->setMaxLength(200)
                ->hideOnIndex(),

            TextField::new('accessToken', 'Access Token')
                ->setRequired(false)
                ->setHelp('授权方access_token')
                ->setMaxLength(300)
                ->hideOnIndex(),

            DateTimeField::new('tokenExpireTime', 'Token过期时间')
                ->setRequired(false)
                ->setHelp('access_token过期时间')
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->hideOnIndex(),

            AssociationField::new('suite', '关联应用模板')
                ->setRequired(false)
                ->setHelp('授权企业关联的应用模板'),

            TextField::new('token', '代开发Token')
                ->setRequired(false)
                ->setHelp('代开发应用回调Token')
                ->setMaxLength(40)
                ->hideOnIndex(),

            TextField::new('encodingAesKey', '代开发EncodingAESKey')
                ->setRequired(false)
                ->setHelp('代开发应用回调加密密钥')
                ->setMaxLength(120)
                ->hideOnIndex(),

            AssociationField::new('serverMessages', '服务器消息')
                ->setRequired(false)
                ->setHelp('该授权企业的服务器回调消息记录')
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
