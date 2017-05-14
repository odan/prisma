# PSR-7 Full Stack Framework

Strictly PSR-7 oriented php component framework.

[![Latest Version on Packagist](https://img.shields.io/github/release/odan/psr7-full-stack.svg)](https://github.com/odan/psr7-full-stack/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://travis-ci.org/odan/psr7-full-stack.svg?branch=master)](https://travis-ci.org/odan/psr7-full-stack)
[![Coverage Status](https://scrutinizer-ci.com/g/odan/psr7-full-stack/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/odan/psr7-full-stack/code-structure)
[![Quality Score](https://scrutinizer-ci.com/g/odan/psr7-full-stack/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/odan/psr7-full-stack/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/odan/psr7-full-stack.svg)](https://packagist.org/packages/odan/psr7-full-stack)


## Main packages

* **Middleware:** [zendframework/zend-diactoros](https://github.com/zendframework/zend-diactorose)
* **Router:** [league/route](https://packagist.org/packages/league/route)
* **Templates:** [league/plates](https://github.com/thephpleague/plates)
* **Assets Cache (Javascript, CSS):** [odan/plates-asset-cache](https://github.com/odan/plates-asset-cache)
* **Database:** [cakephp/database](https://github.com/cakephp/database)
* **Database migrations:** [robmorgan/phinx](https://github.com/robmorgan/phinx) + [odan/phinx-migrations-generator](https://github.com/odan/phinx-migrations-generator)
* **Text translations:** [symfony/translation](https://github.com/symfony/Translation) + [Poedit](https://poedit.net/)
* **Sessions:** [aura/session](https://github.com/aura/session)
* **Logging:** [monolog/monolog](https://github.com/Seldaek/monolog) 
* **Console:** [symfony/console](https://github.com/symfony/console)
* **Unit tests:** [phpunit/phpunit](https://github.com/sebastianbergmann/phpunit)
* **Directory structure:** [php-pds/skeleton](https://github.com/php-pds/skeleton)

## Quality

This project adheres to [Semantic Versioning](http://semver.org/).

To run the unit tests at the command line, issue `composer install` and then
`phpunit` at the package root. This requires [Composer][] to be available as
`composer`, and [PHPUnit][] to be available as `phpunit`.

This package attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

## Installation

**Step 1:** Create a new project:

```shell
composer create-project --prefer-dist odan/psr7-full-stack demo
```

**Step 2:** Setup

Run the installer script and follow the instructions:

```shell
cd config
php install.php
```

**Step 3:** Login<br>

Open the URL in your browser: http://localhost/<br>
Login with username / password: `admin / admin` or `user / user`.

## Requirements

* PHP 7.0+
* MySQL
* Apache

## Directory structure

**[Standard PHP package skeleton](https://github.com/php-pds/skeleton)**

```
bin/                     # command-line executables
config/                  # configuration files
docs/                    # documentation and examples
public/                  # web server files
resources/               # other resource files
src/                     # PHP source code
tests/                   # test code
vendor/                  # reserved for package managers
```

**Other Directories**

```
build/                   # build and artifact files
resources/locale/        # Translations, poedit files
resources/migrations/    # database migration files
resources/fonts/         # tcpdf fonts
resources/images/        # internal image files
tmp/                     # local temp files
tmp/cache/               # local cache files
tmp/log/                 # local log files
 
public/images/           # image files
public/js/               # java-script files
public/css/              # css files
public/icons/            # ico file
public/fonts/            # web fonts

src/Controller/          # controller classes
src/View/                # html template files
src/Service/             # domain models, service layer
src/Type                 # types
src/Util/                # helper classes and functions
```

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

## Security

If you discover any security related issues, please email instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
[Composer]: http://getcomposer.org/
[PHPUnit]: http://phpunit.de/