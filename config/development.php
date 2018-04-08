<?php

//
// Development environment
//
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Display all errors
$settings['displayErrorDetails'] = true;

$settings['db']['host'] = '{{db_host}}';
$settings['db']['database'] = '{{db_database}}';
$settings['db']['host'] = '127.0.0.1';
$settings['db']['database'] = 'prisma';
$settings['logger']['level'] = \Monolog\Logger::DEBUG;
$settings['assets']['minify'] = 0;
$settings['locale']['cache'] = null;
$settings['twig']['cache_enabled'] = false;
