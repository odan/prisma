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


## Requirements

* PHP 7.0+, Apache, MySQL
* [Apache Ant](http://ant.apache.org/) (recommended)

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

## Documentation

This package is documented [here](./docs/readme.md).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
[Composer]: http://getcomposer.org/
[PHPUnit]: http://phpunit.de/
