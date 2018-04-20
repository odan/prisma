<?php

//
// Configure defaults for the whole application.
//
// Error reporting
error_reporting(0);
ini_set('display_errors', '0');

// Timezone
date_default_timezone_set('Europe/Berlin');

// Slim settings
$config = [
    'httpVersion' => '1.1',
    'responseChunkSize' => 4096,
    'outputBuffering' => 'append',
    'determineRouteBeforeAppMiddleware' => true,
    'displayErrorDetails' => false,
    'addContentLengthHeader' => true,
    'routerCacheFile' => false,
];

// Path settings
$config['root'] = dirname(__DIR__);
$config['temp'] = $config['root'] . '/tmp';
$config['public'] = $config['root'] . '/public';

// Application settings
$config['app'] = [
    'secret' => '44573dbff77082506fe5a6b51105e0f17a10c53c',
];

// Logger settings
$config['logger'] = [
    'name' => 'app',
    'file' => $config['temp'] . '/logs/app.log',
    'level' => \Monolog\Logger::ERROR,
];

// View settings
$config['twig'] = [
    'path' => $config['root'] . '/templates',
    'cache_enabled' => true,
    'cache_path' => $config['temp'] . '/twig-cache',
];

// Assets
$config['assets'] = [
    // Public assets cache directory
    'path' => $config['public'] . '/cache',
    // Cache settings
    'cache_enabled' => true,
    'cache_path' => $config['temp'],
    'cache_name' => 'assets-cache',
    // Enable JavaScript and CSS compression
    'minify' => 1,
];

// Session
$config['session'] = [
    'name' => 'webapp',
    'cache_expire' => 0,
];

// Locale settings
$config['locale'] = [
    'path' => $config['root'] . '/resources/locale',
    'cache' => $config['temp'] . '/locale-cache',
    'locale' => 'en_US',
    'domain' => 'messages',
];

// Phinx settings
$config['phinx'] = [
    'paths' => [
        'migrations' => $config['root'] . '/resources/migrations',
        'seeds' => $config['root'] . '/resources/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'local',
        'local' => [],
    ],
];

// Database settings
$config['db'] = [
    'driver' => 'mysql',
    'host' => 'localhost',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'flags' => [
        PDO::ATTR_PERSISTENT => false,
        // Enable exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Set default fetch mode
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ],
];

// E-Mail settings
$config['smtp'] = [
    'type' => 'smtp',
    'host' => '127.0.0.1',
    'port' => '25',
    'secure' => '',
    'from' => 'from@example.com',
    'from_name' => 'My name',
    'to' => 'to@example.com',
];

// Cli commands
$config['commands'] = [
    \App\Console\ExampleCommand::class,
    \App\Console\InstallCommand::class,
    \App\Console\ResetDatabaseCommand::class,
    \App\Console\MigrateDatabaseCommand::class,
    \App\Console\RefreshDatabaseCommand::class,
    \App\Console\GenerateMigrationCommand::class,
    \App\Console\SeedDatabaseCommand::class,
    \App\Console\CreateMigrationCommand::class,
    \App\Console\ParseTextCommand::class,
    \App\Console\UpdateAssetsCommand::class,
];

return $config;
