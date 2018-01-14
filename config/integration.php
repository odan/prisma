<?php

//
// Travis CI environment
//
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

$config['displayErrorDetails'] = true;

// Database
$config['db']['database'] = 'test';
$config['db']['username'] = 'root';
$config['db']['password'] = '';

$config['routerCacheFile'] = null;
$config['logger']['level'] = \Monolog\Logger::DEBUG;
$config['assets']['minify'] = 0;
$config['locale']['cache'] = null;
$config['twig']['cache_enabled'] = false;

return $config;
