# PSR-7 Full Stack Framework

## Requirements

* PHP 5.6 or 7.0+
* MySQL
* Apache with mod_rewrite

## Middlware and packages

* [Zend Diactoros](https://github.com/zendframework/zend-diactoros) - PSR-7 HTTP Message implementation.
* [Relay](https://github.com/relayphp/Relay.Relay) - A PSR-7 middleware dispatcher.
* [FastRoute](https://github.com/nikic/FastRoute) - Fast request router for PHP.
* [Plates](https://github.com/thephpleague/plates) - Native PHP template system.
* [Plates Assets Cache Extension](https://github.com/odan/plates-asset-cache) - Caching and compression of assets (JavaScript and CSS).
* [CakePHP Database](https://github.com/cakephp/database) - Flexible Query builder  with a familiar PDO-like API.
* [Symfony Translation](https://github.com/symfony/Translation) - Localization and translation component.
* [JSON-RPC 2.0](http://www.jsonrpc.org/specification) middleware.
* [Phinx](https://github.com/robmorgan/phinx) - Database migrations.
* [Monolog](https://github.com/Seldaek/monolog) - Logging.
* [PHPMailer ](https://github.com/PHPMailer/PHPMailer) - The classic email sending library.
* [PHPUnit ](https://github.com/sebastianbergmann/phpunit) - Unit tests.

## Installation

```
composer create-project odan/psr7-full-stack.git .
composer install

cd bin
php migration.php migrate
```

##License

MIT
