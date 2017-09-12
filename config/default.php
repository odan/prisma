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

// Path settings
$config['root'] = dirname(__DIR__);
$config['temp'] = $config['root'] . '/tmp';
$config['public'] = $config['root'] . '/public';

// Slim Settings
$config['displayErrorDetails'] = false;
$config['determineRouteBeforeAppMiddleware'] = true;

// Application settings
$config['app'] = [
    'secret' => '{{app_secret}}'
];

// Logger settings
$config['logger'] = [
    'name' => 'app',
    'file' => $config['temp'] . '/log/app.log',
    'level' => \Monolog\Logger::ERROR
];

// Cache settings
$config['cache'] = [
    'path' => $config['temp'] . '/cache'
];

// View settings
$config['view'] = [
    'path' => $config['root'] . '/src/Template'
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

// Database settings
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

// E-Mail settings
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
