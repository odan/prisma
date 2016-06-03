<?php

//
// Development environment
//
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

$config = [];

// Logger
$config['log']['level'] = \Monolog\Logger::DEBUG;

// Database
$config['db']['database'] = 'dev_dbname';

return $config;
