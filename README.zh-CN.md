# 企业微信服务商包

[![PHP Version](https://img.shields.io/packagist/php-v/tourze/wechat-work-provider-bundle)](
https://packagist.org/packages/tourze/wechat-work-provider-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-work-provider-bundle)](
https://packagist.org/packages/tourze/wechat-work-provider-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml)](
https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo)](
https://codecov.io/github/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

适用于 Symfony 应用的企业微信服务商管理包。该包管理企业微信服务商功能，包括授权、
令牌管理和企业同步。

## 功能特性

- 企业微信服务商应用的授权管理
- 自动刷新授权企业的访问令牌
- 企业信息同步
- 支持多个授权企业
- 基于 Symfony Messenger 的事件驱动架构

## 安装

该包需要以下依赖关系：

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+
- tourze/wechat-work-bundle
- tourze/wechat-work-contracts
- tourze/wechat-work-server-bundle

通过 Composer 安装：

```bash
composer require tourze/wechat-work-provider-bundle
```

## 配置

该包使用自动服务配置。将包添加到你的 `config/bundles.php`：

```php
return [
    // ...
    WechatWorkProviderBundle\WechatWorkProviderBundle::class => ['all' => true],
];
```

## 使用方法

### 命令行工具

#### 刷新授权企业 Access Token

刷新所有授权企业的访问令牌：

```bash
php bin/console wechat-work-provider:refresh-auth-corp-access-token
```

此命令通过 cron 每分钟运行一次，以确保令牌始终有效。它会在过期前 5 分钟自动刷新令牌。

#### 同步企业信息

将授权企业信息同步到 WechatWorkBundle：

```bash
php bin/console wechat-work-provider:sync-corp-info
```

此命令通过 cron 每分钟运行一次，以保持企业信息同步和最新。

## 高级用法

### 事件监听器

该包提供各种事件监听器来处理企业微信服务商事件：

- `AuthCorpListener` - 处理企业授权事件
- `SuiteListener` - 处理套件应用事件
- `WechatWorkSubscriber` - 通用企业微信事件订阅器

### 服务扩展

你可以通过覆盖服务或添加自定义事件监听器来扩展包的功能。

## 架构

### 核心概念

- **服务商模板**：为企业开发自定义应用的模板，与授权二维码一一对应
- **多企业支持**：一个服务商模板可以被多个企业授权
- **单应用模板**：每个模板在每个企业中只能开发一个应用

### 为什么要同步到 WechatWorkBundle？

为了提高代码复用性，我们定期将授权企业信息同步到 WechatWorkBundle，
允许在不同应用之间共享功能。

## 安全性

### 安全考虑

- 所有 API 通信使用 HTTPS
- 令牌安全存储并自动刷新
- 回调端点验证请求签名
- 敏感信息经过适当加密

### 报告安全问题

如果你在此包中发现安全漏洞，请发送电子邮件给开发团队。
所有安全漏洞将得到及时处理。

## 测试

运行测试套件：

```bash
./vendor/bin/phpunit packages/wechat-work-provider-bundle/tests
```

运行 PHPStan 分析：

```bash
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/wechat-work-provider-bundle
```

## 贡献

欢迎贡献！请确保你的代码遵循项目的编码标准并包含适当的测试。

## 许可协议

此包是根据 [MIT 许可协议](LICENSE) 开源的软件。