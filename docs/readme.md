# Documentation

## Table of contents

* [Introduction](#introduction)
* [Installation](#installation)
  * [Manual Setup](#manual-setup)
  * [Vagrant Setup](#vagrant-setup)
  * [Docker Setup](#docker-setup)
* [Configuration](#configuration)
* [Directory structure](#directory-structure)
* [Routing](#routing)
* [Middleware](#middleware)
* [Controllers](#controllers)
* [Errors and logging](#errors-and-logging)
* [Frontend](#frontend)
  * [Twig Templates](#twig-templates)
  * [Internationalization](#internationalization)
  * [Translations](#translations)
  * [Localization](#localization)
  * [Updating Assets](#updating-assets)
* [Database](#database)
  * [Database configuration](#database-configuration)
  * [Query Builder](#query-builder)
  * [Repositories](#repositories)
  * [Domain Services](#domain-services)
  * [Migrations](#migrations)
  * [Update schema](#update-schema)
  * [Data Seeding](#data-seeding)
  * [Resetting the database](#resetting-the-database)
* [Security](#security)
  * [Session](#session)
  * [Authentication](#authentication)
  * [Authorization](#authorization)
  * [CSRF Protection](#csrf-protection)
* [Testing](#testing)
  * [Unit tests](#unit-testing)
  * [HTTP Tests](#http-tests)
  * [Database Testing](#database-testing)
  * [Mocking](#mocking)
* [Deployment](#deployment)
  
## Introduction

A skeleton project for Slim 3.

This is a Slim 3 skeleton project that includes Routing, Middleware, Twig templates, 
Translations, Assets, Sessions, Database Queries, Migrations, 
Console Commands, Authentication, Authorization, CSRF protection, 
Logging and Unit testing.

## Installation

### Manual Setup

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
sudo php slim install
```

**Step 4:** Run it

* Open `http://localhost/my-app`
* Login with username / password: `admin / admin` or `user / user`

### Vagrant Setup

* Create a file `vagrantfile`:

```vagrantfile
# -*- mode: ruby -*-
# vi: set ft=ruby :
Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/bionic64"
  config.vm.provision :shell, path: "bootstrap.sh"
  config.vm.network "forwarded_port", guest: 80, host: 8765
  config.vm.provider "virtualbox" do |vb|
    vb.memory = "1024"
    vb.customize ['modifyvm', :id, '--cableconnected1', 'on']
  end  
end
```

* Create a file: `bootstrap.sh`

```sh
#!/usr/bin/env bash

apt-get update
apt-get install vim -y

# unzip is for composer
apt-get install unzip -y

# apache ant (optional)
#apt-get install ant -y

apt-get install apache2 -y

if ! [ -L /var/www ]; then
  rm -rf /var/www
  ln -fs /vagrant /var/www
fi

apt-get install mysql-server mysql-client libmysqlclient-dev -y
apt-get install libapache2-mod-php7.2 php7.2 php7.2-mysql php7.2-sqlite -y
apt-get install php7.2-mbstring php7.2-curl php7.2-intl php7.2-gd php7.2-zip php7.2-bz2 -y
apt-get install php7.2-dom php7.2-xml php7.2-soap -y

# Enable apache mod_rewrite
a2enmod rewrite
a2enmod actions

# Change AllowOverride from None to All (between line 170 and 174)
sed -i '170,174 s/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Start the webserver
service apache2 restart

# Change mysql root password
service mysql start
mysql -u root --password="" -e "update mysql.user set authentication_string=password(''), plugin='mysql_native_password' where user='root';"
mysql -u root --password="" -e "flush privileges;"

# Install composer
cd ~
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
composer self-update

# Create a new project:
mkdir /var/www/html
cd /var/www/html
composer create-project --prefer-dist --no-interaction --no-progress odan/prisma .

# Set permissions
chown -R www-data tmp/
chown -R www-data public/cache/

chmod -R 760 tmp/
chmod -R 760 public/cache/

#chmod +x slim
php slim install --environment ci

vendor/bin/phpunit
```

* Run `vagrant up` 
* Open http://localhost:8765
* Login: username= `user`, password = `user`
* Login as admin: username = `admin`, password = `admin`

### Docker Setup

* todo

## Configuration

### Environment configuration

All the app environments variables are stored in the `env.php` file.

The command `php slim install` will copy `env.example.php` to `env.php` which should have 
your own variables and never shared or committed into git.

Just rename the file `env.example.php` to `env.php`.

## Directory structure

```
.
├── build                   # Compiled files (artifacts)
├── config                  # Configuration files
├── docs                    # Documentation files
├── public                  # Web server files
├── resources               # Other resource files
│   ├── assets              # Raw, un-compiled assets such as LESS, SASS and JavaScript
│   ├── locale              # Language files (translations)
│   ├── migrations          # Database migration files (Phinx)
│   └── seeds               # Data seeds
├── src                     # PHP source code (The App namespace)
│   ├── Action              # Controller actions
│   ├── Console             # Console commands for cli.php
│   ├── Domain              # Business logic
│   ├── Repository          # Data access logic. Communication with the database.
│   ├── Type                # Types, Enum Constants
│   └── Utility             # Helper classes and functions
├── templates               # Twig and Vue templates + JS and CSS
├── tests                   # Automated tests
├── tmp                     # Temporary files
│   ├── assets-cache        # Internal assets cache
│   ├── locale-cache        # Locale cache
│   ├── logs                # Log files
│   ├── routes-cache        # Slim router cache files
│   └── twig-cache          # Internal twig cache
├── vendor                  # Reserved for composer
├── build.xml               # Ant build tasks
├── composer.json           # Project dependencies
├── LICENSE                 # The license
├── slim                    # The command line tool
└── README.md               # This file
```

## Routing

All requests go through the same cycle:  `routing > middleware > conroller/action > response`

### Routes

All the app routes are defined in the [routes.php](https://github.com/odan/prisma/blob/master/config/routes.php) file.

The Slim `$app` variable is responsible for registering the routes. 
You will notice that most routes are enclosed in the `group` method which gives the prefix to the most routes.

Every route is defined by a method corresponds to the HTTP verb. For example, a post request to register a user is defined by:

```php
$this->get('/users', \App\Action\UserIndexAction::class);
```
> Notice: we use `$this` because where are inside a closure that is bound to `$app`; 

## Middleware

In a Slim app, you can add middleware to all incoming routes, to a specific route, or to a group of routes. [Check the documentations](https://www.slimframework.com/docs/concepts/middleware.html) 

In this app we add some middleware to specific routes.

Also, we add some global middleware to apply to all requests in [middleware.php](https://github.com/odan/prisma/blob/master/config/middleware.php).

## Controllers

After passing through all assigned middleware, the request will be processed by a controller / action.

The Controller's job is to translate incoming requests into outgoing responses. 

In order to do this, the controller must take request data, checks for authorization,
and pass it into the domain service layer.

The domain service layer then returns data that the Controller injects into a View for rendering. 

This view might be HTML for a standard web request; or, 
it might be something like JSON for a RESTful API request.

The application uses `Single Action Controllers` which means: one action per class.

A typical action method signature should look like this:

```php
public function __invoke(Request $request, Response $response): ResponseInterface
```

Slim framework will inject all dependencies for you automatically (via constructor injection)
by passing the container instance into the constructor.

Action example class:

```php
<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ExampleAction
{
     /**
      * @var LoggerInterface
      */
    protected $logger;
    
    public function __construct(Container $container)
    {
        // Fetch only dependencies you need for this action
        $this->logger = $container->get(LoggerInterface::class);
        // ...
    }
    
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        return $response->withJson(['success' => true]);
    }
}
```

This concept will produce more class files, but these classes have only one responsibility (SRP).
Refactoring action classes is very easy now, because the routes in `routes.php` make use of the `::class` constant. 

## Errors and logging
 
Depending on the settings all warnings and errors will be logged in the `tmp/logs` direcory.

The default logging settings for your application is stored in the `config/defaults.php` > `logger` configuration file. 

Of course, you may modify this values to suit the needs of your application. 

## Frontend

### Twig Templates

Twig is the simple, yet powerful templating engine provided by Symfony. 

In fact, all Twig views are compiled into plain PHP code and 
cached until they are modified, meaning Twig adds essentially 
zero overhead to your application. 

Twig view files use the `.twig` file extension and are typically stored in the `templates/` directory.

### Localization

The integrated localization features provide a convenient way to retrieve strings 
in various languages, allowing you to easily support multiple languages within 
your application. 

Language strings are stored in files within the `resources/locale` directory. 

Within this directory there should be a `mo` and `po` file for each language supported by the application.

The source language is always english. You don't need a translation file for english.

Example:

* de_DE_messages.mo
* de_DE_messages.po
* fr_FR_messages.mo
* fr_FR_messages.po

#### Configure Localization

* todo: Add description how to add more languages

#### Determining The Current Locale

You may use the getLocale and isLocale methods on the App facade to determine 
the current locale or check if the locale is a given value:

```php
$locale = $this->locale->getLocale(); // en_US
```

#### Defining Translation Strings

To parse all translation strings run:

```bash
$ ant parse-text
```

This command will scan your twig templates, javascripts and PHP classes for the `__()` 
function call and stores all text entries into po-files. 

You can find all-po files in the: `resources/locale` directory. 

[PoEdit](https://poedit.net/) is the recommended PO-file editor for the generated po-files.
 

#### Retrieving Translation Strings

You may retrieve lines from language files using the `__` php helper function. 

The `__` method accepts the text of the translation string as its first argument. 

```php
echo __('I love programming.');
```

Translate a text with a placeholder in PHP:

```php
echo __('There are %s users logged in.', 7);
```

Of course if you are using the **Twig** templating engine, you may use 
the `__` helper function to echo the translation string.

Translate a text:

```twig
⦃⦃ __('Yes') ⦄⦄
```

Translate a text with a placeholder:

```twig
⦃⦃ __('Hello: %s', username) ⦄⦄
```

[Read more](https://github.com/odan/twig-translation#usage)

### Updating Assets

To update all main assets like jquery and bootrap run:

```bash
$ ant update-assets
```

You can add more assets in `package.json` or diretly via `npm`.

Open the file `build.xml` and navigate to the target `update-assets` 
and add more items to copy the required files into the `public` directory.

## Database

### Database configuration

* You may configure the database settings per server environment.
* The global default settings are stored in `config/defaults.php` > `$settings['db']` 

### Query Builder

This application comes with [cakephp/database](https://github.com/cakephp/database) as SQL query builder.

The database query builder provides a convenient, fluent interface to creating and running database queries. It can be used to perform most database operations in your application, and works great with MySQL and MariaDB.

For more details how to build queries read the **[documentation](https://book.cakephp.org/3.0/en/orm/query-builder.html)**.

### Repositories

* Communication with the database.
* Data access logic / query logic
* No business logic here! (Use Services for business logic)

### Domain Services

Here is the right place for complex business logic e.g. calulation, validation, file creation etc.

This layer provides cohesive, high-level logic for related
parts of an application. This layer is invoked directly by
the Controllers.

The business logic should be placed in the service classes,
and we should be aiming for fat models and skinny controllers.

### Migrations

This skeleton project provides console access for **[Phinx](https://phinx.org/)** to 
create database migrations. 

#### Generating a migration from a diff automatically

```bash
$ ant generate-migration
```

#### Creating a blank database migration

```bash
$ ant create-migration
```

For more details how to create and manage migrations read the 
[Phinx](http://docs.phinx.org/en/latest/) documentation.

### Update schema

Updating the database schema with this shorthand command:

```bash
$ ant migrate-database
```

If `ant` is not installed on the target server, this command can also be used:

```bash
$ vendor/bin/phinx migrate -c config/phinx.php
```

### Data Seeding

To populate the database with data for testing and experimenting with the code run:

```bash
$ ant seed-database
```

If `ant` is not installed, you can run this command:

```bash
$ vendor/bin/phinx seed:run -c config/phinx.php
```

You may add more seeds under the directory: `resources\seeds\DataSeed`.

### Resetting the database

The command `refresh-database` will rollback all migrations, 
migrate the database and seed the data. 

**Attention: All data will be lost from the database.**

```
$ ant refresh-database
```

## Security

## Session

This application uses `sessions` to store the logged-in user information. If you 
have to add api routes you may use `JWT` or a `OAuth2 Bearer-Token`.

### Authentication

The authentication depends on the defined routes and the attached middleware.
You can add routing groups with Sessions and/or OAuth2 authentication. 
It's up to you how you configure the routes and their individual authentication.

### Authorization

To check user permissions, the Actions controller contains an `Auth` object.

Determine the logged-in user ID::

```php
$userId = $this->auth->getUserId();
```

Checking the user role (permission group):

```php
$isAdmin = $this->auth->hasRole(UserRole::ROLE_ADMIN);
```

### CSRF Protection

All session based requests are protected against Cross-site request forgery (CSRF).

## Testing

### Unit testing

All tests are located in the `test/` folder. To start the unit test run:

``` bash
$ ant phpunit
```

### Debugging unit tests with PhpStorm

To debug tests with PhpStorm you must have to mark the directory `test/` 
as the test root source.

* Open the project in PhpStorm
* Right click the directory `tests` 
* Select: `Mark directory as`
* Click `Test Sources Root`
* Set a breakpoint within a test method
* Right click `test`
* Click `Debug (tests) PHPUnit`

### HTTP tests

Everything is prepared to run mocked http tests.

Please take a look at the example tests in:

* `tests/TestCase/HomeIndexActionTest.php`
* `test/TestCase/HomePingActionTest.php`

## Database Testing

* todo: Add integration tests

## Mocking

There is no special mocking example available. 

Just use the [PHPUnit mocking functionality](https://phpunit.de/manual/current/en/test-doubles.html)
or install other mocking libraries you want. Feel free.

## Deployment

### Building an artifact

To build a new artifact (ZIP file) which is tested and ready for deployment run:

``` bash
$ ant build
```

The new artifact is created in the `build` directory: `build/my_app_*.zip`

To deploy the artifact to test/staging or production just upload
the zip file with a sftp client onto your server (`/var/www/example.com`).
Then extract the artifact into a `htdocs` sub-directory and run the migrations. 
You can use `deploy.php` for this task.

#### Server setup

* Create a directory: `/var/www/example.com`
* Create a directory: `/var/www/example.com/htdocs`
* Upload, rename and customize: `config/env.example.php` to `/var/www/example.com/env.php`
* Upload `config/deploy.php` to `/var/www/example.com/deploy.php`
* Make sure the apache [DocumentRoot](https://httpd.apache.org/docs/2.4/en/mod/core.html#documentroot) points to the `public` path: `/var/www/example.com/htdocs/public`

#### Deploying a new artifact

* Upload the new artifact file `my_app_*.zip` to `/var/www/example.com/`
* Then run `sudo php deploy.php my_app_*.zip`

Example:

```bash
$ cd /var/www/example.com
$ sudo php deploy.php my_app_2019-01-29_235044.zip
```
