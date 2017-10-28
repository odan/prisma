# Prisma

This is a simple skeleton project for Slim 3 that includes Plates, Sessions and Monolog.

[![Latest Version on Packagist](https://img.shields.io/github/release/odan/prisma.svg)](https://github.com/odan/prisma/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://travis-ci.org/odan/prisma.svg?branch=master)](https://travis-ci.org/odan/prisma)
[![Quality Score](https://scrutinizer-ci.com/g/odan/prisma/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/odan/prisma/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/odan/prisma.svg)](https://packagist.org/packages/odan/prisma)


## Main packages

* Router, Middleware, PSR-7 HTTP messages - [slimphp/slim](https://github.com/slimphp/Slim)
* Sessions - [aura/session](https://github.com/auraphp/Aura.Session)
* Dependency Injection with Autowiring - [php-di/php-di](https://github.com/PHP-DI/PHP-DI)
* Template Engine - [league/plates](https://github.com/thephpleague/plates)
* Assets Cache - [odan/plates-asset-cache](https://github.com/odan/plates-asset-cache)
* Database Query Builder - [odan/database](https://github.com/odan/database)
* Migrations - [cakephp/phinx](https://github.com/cakephp/phinx)
* Migrations Generator - [odan/phinx-migrations-generator](https://github.com/odan/phinx-migrations-generator)
* Hydrator - [odan/hydrator](https://github.com/odan/hydrator)
* Translations - [symfony/translation](https://github.com/symfony/Translation)
* Logging - [monolog/monolog](https://github.com/Seldaek/monolog) 
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

| Root-Directory | Sub-Directory | Content |
|----------|----------|-------------|
| build | | Artifact files |
| bin | | Command-line executables |
| config | | Configuration files |
| docs | | Documentation and examples |
| public | | Web server files |
| resources | | Other resource files |
| | assets | Raw, un-compiled assets such as LESS, SASS, or JavaScript |
| | locale | The language files (translations) |
| | migrations | Database migration files (Phinx) |
| src | | PHP source code (The App namespace) |
| | Controller | Controllers and actions |
| | Entity | Entities (Represents a table row) |
| | Repository | Repositories (Communication with the database) |
| | Service | Business logic |
| | Table | The Table Gateway (Represents a table) |
| | Template | The views (HTML templates) |
| | Utility | Helper classes |
| tests | | Test code |
| tmp | | Temporary files |
| | logs | Log files |
| | assets-cache | Internal assets cache |
| vendor | | Reserved for composer |

## Routing

You can define custom routes in [config/routes.php](config/routes.php). 

## SQL Query Builder

This framework comes with [odan/database](https://github.com/odan/database) as SQL query builder.

The database query builder provides a convenient, fluent interface to creating and running database queries. It can be used to perform most database operations in your application, and works great with MySQL and MariaDB.

For more details how to build queries read the **[documentation](https://github.com/odan/database/blob/master/docs/index.md)**.

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

## Environment configuration

You can keep sensitive information's out of version control with a separate ´env.php´ for each environment.

You should store all sensitive information in 'env.php' and add the file to your .gitignore, so that you don't accidentally commit to source control.

Just rename the file 'env.example.php' to 'env.php'

## Deployment

### Continuous Delivery

You can build artifact's (ZIP files) which are tested and ready for deployment.

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
