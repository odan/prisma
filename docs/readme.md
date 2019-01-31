# Documentation

## Table of contents

* [Introduction](#introduction)
* [Installation](#installation)
  * [Manual Setup](#manual-setup)
  * [Vagrant Setup](#vagrant-setup)
  * [Docker Setup](#docker-setup)
* Configuration
* The Basics
  * [Directory structure](#directory-structure)
  * [Routing](#routing)
  * Middleware
  * Controllers
  * Session
  * Validation
  * Errors and logging
* Frontend
  * Twig Templates
  * [Internationalization](#internationalization)
  * Localization
  * [Updating Assets](#updating-assets)
* Database
  * Configuration
  * [Query Builder](#query-builder)
  * Services
  * Repositories
  * Types and Enums
  * [Migrations](#migrations)
  * [Data Seeding](#data-seeding)
* Security
  * Authentication
  * Authorization
  * CSRF Protection
* [Testing](#testing)
  * [Unit tests](#unit-testing)
  * HTTP Tests
  * [Database Testing](#database-testing)
  * Mocking

## Introduction

A skeleton project for Slim 3.

This is a Slim 3 skeleton project that includes Routing, Middleware, Twig templates, 
mustache.js, Translations, Assets, Sessions, Database Queries, Migrations, 
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
sudo php bin/cli.php install
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

#chmod +x bin/cli.php
php bin/cli.php install --environment travis

vendor/bin/phpunit
```

* Run `vagrant up` 
* Run `vagrant ssh`
* Open http://localhost:8765
* Login: username= `user`, password = `user`
* Login as admin: username = `admin`, password = `admin`

### Docker Setup

todo

## Directory structure

```
.
├── bin                     # Console applications
│   └── cli.php             # The main command line tool (php bin/cli.php)
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
│   ├── Data                # Data transfer objects (DTO)
│   ├── Domain              # Business logic
│   ├── Repository          # Data access logic. Communication with the database.
│   ├── Type                # Types, Enum Constants
│   └── Utility             # Helper classes and functions
├── templates               # Twig and Mustache templates + JS and CSS
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
└── README.md               # This file
```

## Routing

You can define custom routes in [config/routes.php](https://github.com/odan/prisma/blob/master/config/routes.phpp). 

## Internationalization

To parse all the text run:

```bash
$ ant parse-text
```

This command will scan your twig templates, javascripts and PHP classes for the `__()` function call and stores all text entries into the po file. You can find all po file here: `resources/locale`. Use [PoEdit](https://poedit.net/) to open and translate the po files.

## Updating Assets

To update all main assets like jquery and bootrap run:

```bash
$ ant update-assets
```

You can add more assets in `package.json` or diretly via `npm`.

Open the file `build.xml` and navigate to the target `update-assets` 
and add more items to copy the required files into the `public` directory.

## Environment configuration

You can keep sensitive information's out of version control with a separate `env.php` for each environment.

You should store all sensitive information in `env.php` and add the file to your `.gitignore`, so that you do not accidentally commit it to the source control.

Just rename the file `env.example.php` to `env.php`.

### Query Builder

This framework comes with [cakephp/database](https://github.com/cakephp/database) as SQL query builder.

The database query builder provides a convenient, fluent interface to creating and running database queries. It can be used to perform most database operations in your application, and works great with MySQL and MariaDB.

For more details how to build queries read the **[documentation](https://book.cakephp.org/3.0/en/orm/query-builder.html)**.

### Migrations

This skeleton project provides console access for **[Phinx](https://phinx.org/)** to create database migrations. 

To create a new migration manually:

```bash
$ ant create-migration
```

To generate a new migration automatically:

```bash
$ ant generate-migration
```

For more details how to create and manage migrations read the [Phinx](http://docs.phinx.org/en/latest/) documentation.

### Data Seeding

To populate the database with data for testing and experimenting with the code. Run:

```bash
vendor/bin/phinx seed:run -c config/phinx.php
```

or just

```bash
ant seed-database
```

To edit how the data is seeded check the file: `resources\seeds\DataSeed`.

The command `refresh-database` will rollback all migrations, migrate the database and seed the data. 

Note: all data will be lost from the database.

```
ant refresh-database
```

## Testing

### Unit testing

``` bash
$ ant phpunit
```

### Database testing

##  Database

### Database configuration

### Continuous Delivery

You can build artifact's (ZIP files) which are tested and ready for deployment.

``` bash
$ ant build
```
