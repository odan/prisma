# Documentation

## Table of contents

* Introduction
* Getting started
 * Installation
 * Configuration
 * [Directory structure](#directory-structure)
 * [Deployment](#deployment)
* The Basics
  * [Routing](#routing)
  * Middleware
  * Controllers
  * Session
  * Validation
  * Errors and logging
* Frontend
  * Twig Templates
  * Mustache Templates
  * Localization
  * Compiling Assets
* Database
  * Configuration
  * [Query Builder](#query-builder)
  * Data Mapper
  * Entities
  * Types and Enums
  * [Migrations](#migrations)
  * [Data Seeding](#data-seeding)
* Security
  * Authentication
  * Authorization
  * CSRF Protection
* Testing
  * [Unit tests](#Testing)
  * HTTP Tests
  * [Database Testing](#database-testing)
  * Mocking


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
│   ├── Command             # Console commands for cli.php
│   ├── Entity              # Represents individual rows or domain objects in your application
│   ├── Service             # Business logic
│   ├── Table               # Table specific data mapper. Communication with the database.
│   ├── Type                # Types, Enum Constants
│   └── Utility             # Helper classes
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
├── cli.php                 # Command line tool (php cli.php)
├── LICENSE                 # The license
└── README.md               # This file
```

## Routing

You can define custom routes in [config/routes.php](config/routes.php). 

## Environment configuration

You can keep sensitive information's out of version control with a separate `env.php` for each environment.

You should store all sensitive information in `env.php` and add the file to your `.gitignore`, so that you do not accidentally commit it to the source control.

Just rename the file `env.example.php` to `env.php`.

### Query Builder

This framework comes with [illuminate/database](https://github.com/illuminate/database) as SQL query builder.

The database query builder provides a convenient, fluent interface to creating and running database queries. It can be used to perform most database operations in your application, and works great with MySQL and MariaDB.

For more details how to build queries read the **[documentation](https://laravel.com/docs/master/queries)**.

### Migrations

This skeleton project provides console access for **[Phinx](https://phinx.org/)** to create database migrations. 

To create a new migration manually:

```bash
$ php cli.php create-migration
```

To generate a new migration automatically:

```bash
$ php cli.php generate-migration
```

For more details how to create and manage migrations read the [Phinx](http://docs.phinx.org/en/latest/) documentation.

### Data Seeding

To populate the database with data for testing and experimenting with the code. Run:

```
php cli.php seed-database
```

To edit how the data is seeded check the file: `resources\seeds\DataSeed`.

The command `refresh-database` will rollback all migrations, migrate the database and seed the data. 

Note: all data will be lost from the database.

```
php cli.php refresh-database
```

## Deployment

### Continuous Delivery

You can build artifact's (ZIP files) which are tested and ready for deployment.

``` bash
$ ant build
```

Upload to production:

``` bash
$ ant deploy
```

## Testing

### Unit testing

``` bash
$ ant phpunit
```

### Database testing

##  Database

### Database configuration
