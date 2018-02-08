# Prisma

This is a Slim 3 skeleton project that includes Routing, Middleware,
Twig templates, mustache.js, Translations, Assets, Sessions, Database Queries, 
Migrations, Console Commands, Authentication, Authorization, CSRF protection, 
Logging and Unit testing.

[![Latest Version on Packagist](https://img.shields.io/github/release/odan/prisma.svg)](https://github.com/odan/prisma/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://travis-ci.org/odan/prisma.svg?branch=master)](https://travis-ci.org/odan/prisma)
[![Quality Score](https://scrutinizer-ci.com/g/odan/prisma/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/odan/prisma/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/odan/prisma.svg)](https://packagist.org/packages/odan/prisma)


## Main packages

* Router, Middleware, PSR-7 HTTP messages - [slimphp/slim](https://github.com/slimphp/Slim)
* Template Engine - [slim/twig-view](https://github.com/slimphp/Twig-View)
* Assets Cache - [odan/twig-assets](https://github.com/odan/twig-assets)
* Sessions - [odan/slim-session](https://github.com/odan/slim-session)
* Database Query Builder - [illuminate/database](https://github.com/illuminate/database)
* Migrations - [cakephp/phinx](https://github.com/cakephp/phinx)
* Migrations Generator - [odan/phinx-migrations-generator](https://github.com/odan/phinx-migrations-generator)
* Translations - [symfony/translation](https://github.com/symfony/Translation), [odan/twig-translation](https://github.com/odan/twig-translation)
* Logging - [monolog/monolog](https://github.com/Seldaek/monolog) 
* Unit tests - [phpunit/phpunit](https://github.com/sebastianbergmann/phpunit)
* Directory structure - [php-pds/skeleton](https://github.com/php-pds/skeleton)

## Requirements

* PHP 7.0+
* Apache
* MySQL
* [Apache Ant](http://ant.apache.org/)

## Installation

**Step 1:** Create a new project:

```shell
composer create-project --prefer-dist odan/prisma my-app
```

**Step 2:** Setup

Run the installer script and follow the instructions:

```shell
php cli.php install
```

**Step 3:** Run it<br>

* You don't have to start the PHP built-in web server. Just open the local url e.g. http://localhost and navigate to the page.
* Login with username / password: `admin / admin` or `user / user`

## Directory structure

| Root-Directory | Sub-Directory | Content |
|----------|----------|-------------|
| build | | Artifact files |
| config | | Configuration files |
| docs | | Documentation and examples |
| public | | Web server files |
| resources | | Other resource files |
| | assets | Raw, un-compiled assets such as LESS, SASS and JavaScript |
| | locale | Language files (translations) |
| | migrations | Database migration files (Phinx) |
| src | | PHP source code (The App namespace) |
| | Controller | Controllers and actions |
| | Table | Table specific data mapper. Decouples the domain objects completely from the persistent storage. (Communication with the database, query methods) |
| | Entity | Represents a row of data |
| | Middleware | HTTP middleware |
| | Service | Business logic |
| | Type | Types, Enum Constants |
| | Utility | Helper classes |
| templates | | Twig and Mustache templates + JS and CSS
| tests | | Test code |
| tmp | | Temporary files |
| | logs | Log files |
| | twig-cache | Internal twig cache |
| | assets-cache | Internal assets cache |
| vendor | | Reserved for composer |

## Routing

You can define custom routes in [config/routes.php](config/routes.php). 

## SQL Query Builder

This framework comes with [illuminate/database](https://github.com/illuminate/database) as SQL query builder.

The database query builder provides a convenient, fluent interface to creating and running database queries. It can be used to perform most database operations in your application, and works great with MySQL and MariaDB.

For more details how to build queries read the **[documentation](https://laravel.com/docs/master/queries)**.

## Migrations

This skeleton project provides console access for **[Phinx](https://phinx.org/)** to create database migrations. 

To create a new migration manually:

```bash
$ php cli.php phinx create MyNewMigration
```

To generate a new migration automatically:

```bash
$ php cli.php phinx generate
```

For more details how to create and manage migrations read the [Phinx](http://docs.phinx.org/en/latest/) documentation.

## Environment configuration

You can keep sensitive information's out of version control with a separate `env.php` for each environment.

You should store all sensitive information in `env.php` and add the file to your `.gitignore`, so that you do not accidentally commit it to the source control.

Just rename the file `env.example.php` to `env.php`.

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
$ ant phpunit
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
