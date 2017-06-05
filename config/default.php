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
$config['determineRouteBeforeAppMiddleware'] = false;

// Path
$config['root'] = dirname(__DIR__);
$config['temp'] = $config['root'] . '/tmp';
$config['public'] = $config['root'] . '/public';

// Application token
$config['app'] = [
    'secret' => $config['root'] . '{{app_secret}}'
];

// Logger settings
$config['logger'] = [
    'name' => 'app',
    'path' => $config['temp'] . '/log/app.log',
    'level' => \Monolog\Logger::ERROR
];

// Cache settings
$config['cache'] = [
    'path' => $config['root'] . '/tmp/cache'
];

// View settings
$config['view'] = [
    'path' => $config['root'] . '/src/View'
];

// Assets
$config['assets'] = [
    'path' => $config['public'],
    // Internal cache adapter
    'cache' => new \Symfony\Component\Cache\Adapter\FilesystemAdapter('assets-cache', 0, $config['temp']),
    // Public assets cache directory
    'public_dir' => $config['public'] . '/cache',
    // Enable JavaScript and CSS compression
    'minify' => 1
];


// Session
$config['session'] = [
    'name' => 'webapp',
    'cache_expire' => 0
];

// Locale settings
$config['locale'] = [
    'path' => $config['root'] . '/resources/locale'
];

// Database migration settings
$config['migration'] = [
    'path' => $config['root'] . '/resources/migrations'
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
