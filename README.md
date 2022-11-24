[![Latest Version on Packagist](https://img.shields.io/packagist/v/jalallinux/laravel-postgres-audit.svg?style=flat-square)](https://packagist.org/packages/jalallinux/laravel-postgres-audit)
[![Tests](https://github.com/jalallinux/laravel-postgres-audit/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/jalallinux/laravel-postgres-audit/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/jalallinux/laravel-postgres-audit.svg?style=flat-square)](https://packagist.org/packages/jalallinux/laravel-postgres-audit)
---

Logging queries executed on Postgres even directly!!!

## Installation

You can install the package via composer:

```bash
composer require jalallinux/laravel-postgres-audit
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-postgres-audit-config"
```

This is the contents of the published config file:

```php
return [
/**
     * Target database connection
     */
    'connection' => 'pgsql',


    /**
     * Audit log table name
     */
    'table_name' => 'audit_logs',


    /**
     * Target tables for log
     * Set ['*'] to log for all tables
     * Split tables with comma
     */
    'target_tables' => '*',


    /**
     * Except tables for log
     * Except tables has more priority than target tables
     * Split tables with comma
     */
    'except_tables' => 'migrations, password_resets, personal_access_tokens, jobs, failed_jobs, notifications',


    /**
     * Ignore database user activity
     * Example: postgres, forge
     * NULL for logging all users
     * Split tables with comma
     */
    'except_users' => null,


    /**
     * Primary columns
     * Example: id, uuid, slug
     */
    'primary_columns' => 'uuid',


    /**
     * Primary columns type
     * Example: bigint, uuid, varchar
     */
    'primary_columns_type' => 'uuid',


    /**
     * Operations to log
     * Only support INSERT, DELETE, UPDATE
     */
    'operations' => 'INSERT, DELETE, UPDATE'
];
```

## Usage

### Setup:
```shell
php artisan pg-audit:setup
```

### Model:
Work with `PGAudit` model.
```php
\JalalLinuX\PostgreAudit\PGAudit::where('table_name', 'users')->get()
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [JalalLinuX](https://github.com/jalallinux)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
