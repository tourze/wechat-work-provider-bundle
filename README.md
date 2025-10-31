# WeChat Work Provider Bundle

[![PHP Version](https://img.shields.io/packagist/php-v/tourze/wechat-work-provider-bundle)](
https://packagist.org/packages/tourze/wechat-work-provider-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-work-provider-bundle)](
https://packagist.org/packages/tourze/wechat-work-provider-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml)](
https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo)](
https://codecov.io/github/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

WeChat Work provider service bundle for Symfony applications. This bundle 
manages WeChat Work provider functionalities including authorization, token 
management, and enterprise synchronization.

## Features

- Authorization management for WeChat Work provider applications
- Automatic token refresh for authorized enterprises  
- Enterprise information synchronization
- Support for multiple authorized enterprises
- Event-driven architecture using Symfony Messenger

## Installation

This bundle requires the following dependencies:

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+
- tourze/wechat-work-bundle
- tourze/wechat-work-contracts
- tourze/wechat-work-server-bundle

Install via Composer:

```bash
composer require tourze/wechat-work-provider-bundle
```

## Configuration

The bundle uses automatic service configuration. Add the bundle to your 
`config/bundles.php`:

```php
return [
    // ...
    WechatWorkProviderBundle\WechatWorkProviderBundle::class => ['all' => true],
];
```

## Usage

### Commands

#### Refresh Auth Corp Access Token

Refreshes access tokens for all authorized enterprises:

```bash
php bin/console wechat-work-provider:refresh-auth-corp-access-token
```

This command runs every minute via cron to ensure tokens are always valid. It 
automatically refreshes tokens 5 minutes before expiration.

#### Sync Corp Info

Synchronizes authorized enterprise information to WechatWorkBundle:

```bash
php bin/console wechat-work-provider:sync-corp-info
```

This command runs every minute via cron to keep enterprise information 
synchronized and up-to-date.

## Advanced Usage

### Event Listeners

The bundle provides various event listeners for handling WeChat Work provider 
events:

- `AuthCorpListener` - Handles enterprise authorization events
- `SuiteListener` - Handles suite application events  
- `WechatWorkSubscriber` - General WeChat Work event subscriber

### Service Extensions

You can extend the bundle's functionality by overriding services or adding 
custom event listeners.

## Architecture

### Key Concepts

- **Provider Template**: A template for developing custom applications for 
  enterprises, with one-to-one correspondence to authorization QR codes
- **Multiple Enterprise Support**: One provider template can be authorized by 
  multiple enterprises
- **Single App per Template**: Each template can only develop one application 
  per enterprise

### Why Sync to WechatWorkBundle?

To improve code reusability, we periodically sync authorized enterprise 
information to WechatWorkBundle, allowing shared functionality across different 
applications.

## Security

### Security Considerations

- All API communications use HTTPS
- Tokens are securely stored and automatically refreshed
- Callback endpoints validate request signatures
- Sensitive information is properly encrypted

### Reporting Security Issues

If you discover a security vulnerability within this bundle, please send an 
email to the development team. All security vulnerabilities will be promptly 
addressed.

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit packages/wechat-work-provider-bundle/tests
```

Run PHPStan analysis:

```bash
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/wechat-work-provider-bundle
```

## Contributing

Contributions are welcome! Please ensure your code follows the project's coding standards and includes appropriate tests.

## License

This bundle is open-sourced software licensed under the [MIT license](LICENSE).