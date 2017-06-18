# Prisma

This is a simple skeleton project for Slim 3 that includes Plates, Sessions and Monolog.

[![Latest Version on Packagist](https://img.shields.io/github/release/odan/prisma.svg)](https://github.com/odan/prisma/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://travis-ci.org/odan/prisma.svg?branch=master)](https://travis-ci.org/odan/prisma)
[![Quality Score](https://scrutinizer-ci.com/g/odan/prisma/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/odan/prisma/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/odan/prisma.svg)](https://packagist.org/packages/odan/prisma)


## Main packages

* Template Engine - [league/plates](https://github.com/thephpleague/plates)
* Logging - [monolog/monolog](https://github.com/Seldaek/monolog) 
* Sessions - [aura/session](https://github.com/aura/session)
* Translations - [symfony/translation](https://github.com/symfony/Translation)
* Database - [cakephp/database](https://github.com/cakephp/database)
* Database Migrations - [robmorgan/phinx](https://github.com/robmorgan/phinx)
* Migrations Generator - [odan/phinx-migrations-generator](https://github.com/odan/phinx-migrations-generator)
* Assets Cache - [odan/plates-asset-cache](https://github.com/odan/plates-asset-cache)
* Unit tests - [phpunit/phpunit](https://github.com/sebastianbergmann/phpunit)
* Directory structure - [php-pds/skeleton](https://github.com/php-pds/skeleton)

## Installation

**Step 1:** Create a new project:

```shell
composer create-project --prefer-dist odan/prisma my-app
```

**Step 2:** Setup

Run the installer script and follow the instructions:

```shell
cd config
php install.php
```

**Step 3:** Run it<br>

1. `$ cd my-app/public`
2. `$ php -S 0.0.0.0:8080`
3. Browse to http://localhost:8080
4. Login with username / password: `admin / admin` or `user / user`

## Requirements

* PHP 7.0+
* MySQL

## Directory structure

| Directory  | Content |
|----------|-------------|
| build/ | Artifact files |
| bin/ | Command-line executables |
| config/ | Configuration files |
| docs/ | Documentation and examples |
| public/ | Web server files |
| resources/ | Other resource files |
| src/ | PHP source code |
| src/Controller/ | Controllers and actions |
| src/Model/ | Tables and entities |
| src/Service/ | Service layer, domain models, business and use case logic  |
| src/Template/ | Templates |
| tmp/ | Temp, cache and logfiles |
| tests/ | Test code |
| vendor/ | Reserved for package managers |

## Routing

You can define custom routes in [config/routes.php](config/routes.php). 

## SQL Query Builder

This framework comes with [CakePHP Database](https://github.com/cakephp/database) as SQL query builder.

The database query builder provides a convenient, fluent interface to creating and running database queries. It can be used to perform most database operations in your application, and works on all supported database systems.

For more details how to build queries read the **[documentation](http://book.cakephp.org/3.0/en/orm/query-builder.html)**.

## Migrations

This framework provides console access for **[Phinx](https://phinx.org/)** to create database migrations. 

* To create a new migration manually:

```bash
cd bin/
php phinx.php create
```

* To create a new migration automatically:

```bash
cd bin/
php phinx.php generate
```

For more details how to create and manage migrations read the [Phinx](http://docs.phinx.org/en/latest/) documentation.

## Testing

``` bash
$ composer test
```

## Environment configuration

You can keep sensitive information's out of version control with a separate ´env.php´ for each environment.

You should store all sensitive information in 'env.php' and add the file to your .gitignore, so that you don't accidentally commit to source control.

Just rename the file 'env.example.php' to 'env.php'

## Deployment

### Continuous Delivery

You can build artifact's (ZIP files) which are tested and ready for the next deployment step.

``` bash
$ ant build
```

### Continuous Deployment

``` bash
$ ant deploy
```

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
[Composer]: http://getcomposer.org/
[PHPUnit]: http://phpunit.de/
