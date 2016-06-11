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

// Logger
$config['log'] = array(
    'level' => \Monolog\Logger::ERROR,
    'path' => realpath(__DIR__ . '/../../tmp/log'),
);

// View
$config['view'] = array(
    'view_path' => realpath(__DIR__ . '/../View'),
    'assets_path' => realpath(__DIR__ . '/../../web/assets'),
    'cache_path' => realpath(__DIR__ . '/../../web/cache'),
    'minify' => 0
);

// Session
$config['session'] = array(
    'name' => 'webapp'
);

// Database
$config['db'] = array(
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
    'flags' => [PDO::ATTR_CASE, PDO::CASE_LOWER]
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
