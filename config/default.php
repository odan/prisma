<?php
//
// Configure defaults for the whole application.
//
// Error reporting
error_reporting(0);
ini_set('display_errors', '0');

// Timezone
date_default_timezone_set('Europe/Berlin');

$config = [];

// Slim Settings
$config['displayErrorDetails'] = false;
$config['determineRouteBeforeAppMiddleware'] = true;

// Path
$root = dirname(__DIR__);
$config['root_path'] = $root;
$config['tmp_path'] = $root . '/tmp';
$config['log_path'] = $root . '/tmp/log';
$config['cache_path'] = $root . '/tmp/cache';
$config['public_path'] = $root . '/public';
$config['locale_path'] = $root . '/resources/locale';
$config['migration_path'] = $root . '/resources/migrations';

// Application token
$config['app_secret'] = '6c6bee844f2420ede093af25b58bb8ba8b7dc04d';

// Monolog settings
$config['logger'] = [
    'name' => 'app',
    'path' => __DIR__ . '/../log/app.log',
    'level' => \Monolog\Logger::ERROR
];

// View settings
$config['view'] = [
    'path' => $root . '/src/View'
];

// Assets
$config['assets'] = [
    'path' => $root . '/public',
    // Internal cache adapter
    'cache' => new \Symfony\Component\Cache\Adapter\FilesystemAdapter('assets-cache', 0, $root . '/tmp/cache'),
    // Public assets cache directory
    'public_dir' => $root . '/public/cache',
    // Enable JavaScript and CSS compression
    'minify' => 1
];


// Session
$config['session'] = [
    'name' => 'webapp',
    'cache_expire' => 0
];

// Database
$config['db'] = array(
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
    'flags' => [
        // Enable exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Set default fetch mode
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

// SMTP
$config['smtp'] = array(
    'type' => 'smtp',
    'host' => '127.0.0.1',
    'port' => '25',
    'secure' => '',
    'from' => 'from@example.com',
    'from_name' => 'My name',
    'to' => 'to@example.com',
);

return $config;
