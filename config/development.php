<?php

//
// Development environment
//
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

$config = [];

// Logger
$config['log_level'] = \Monolog\Logger::DEBUG;

// View
$config['assets_minify'] = 0;

// Database
//$config['db']['database'] = 'test';
$config['db']['database'] = 'astro_php';

return $config;
