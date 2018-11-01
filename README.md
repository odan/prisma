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

* PHP 7.2+
* MySQL

## Recommended

* Apache
* [Apache Ant](http://ant.apache.org/)

## Installation

**Step 1:** Create a new project:

```shell
composer create-project --prefer-dist odan/prisma my-app
```

**Step 2:** Set permissions

*(Linux only)*

```bash
cd my-app
```

```bash
sudo chown -R www-data tmp/
sudo chown -R www-data public/cache/
```

*Optional*

NOTE: The app will have ability to create subfolders 
in `tmp/` and `public/cache/` which means it will need 760.

```bash
sudo chmod -R 760 tmp/
sudo chmod -R 760 public/cache/
```

NOTE: Debian/Ubuntu uses `www-data`, while CentOS uses `apache` and OSX `_www`.

**Step 3:** Setup

Run the installer script and follow the instructions:

```shell
sudo php bin/cli.php install
```

**Step 4:** Run it

* Open `http://localhost/my-app`
* Login with username / password: `admin / admin` or `user / user`

## Documentation

This package is documented [here](https://odan.github.io/prisma/).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.


[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
[Composer]: https://getcomposer.org/
[PHPUnit]: https://phpunit.de/
