<?php

//
// Development environment
//
$settings['env'] = 'development';

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Display all errors
$settings['displayErrorDetails'] = true;

$settings['db']['host'] = '{{db_host}}';
$settings['db']['database'] = '{{db_database}}';

$settings['logger']['level'] = \Monolog\Logger::DEBUG;
$settings['assets']['minify'] = 0;
$settings['locale']['cache'] = null;
$settings['twig']['cache_enabled'] = false;
