<?php

//
// Development environment
//
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Display all errors
$config['displayErrorDetails'] = true;

// Logger
$config['logger']['level'] = \Monolog\Logger::DEBUG;

// View
$config['assets']['minify'] = 0;

// Database
$config['db']['database'] = '{{db_database}}';
$config['db']['database'] = 'prisma';

return $config;
