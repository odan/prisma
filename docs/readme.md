# Documentation

## Table of contents

* Introduction
* Getting started
 * Installation
 * Configuration
 * Directory structure
 * Deployment
* The Basics
  * Routing
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
  * Query Builder
  * Data Mapper
  * Entities
  * Types and Enums
  * Migrations
  * [Data Seeding](#data-seeding)
* Security
  * Authentication
  * Authorization
  * CSRF Protection
* Testing
  * Unit tests
  * HTTP Tests
  * Database Testing
  * Mocking
  
  
##  Database

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