<?php

//
// Travis CI environment
//
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

$settings['displayErrorDetails'] = true;

// Database
$settings['db']['database'] = 'test';
$settings['db']['username'] = 'root';
$settings['db']['password'] = '';

$settings['logger']['level'] = \Monolog\Logger::DEBUG;
$settings['assets']['minify'] = 0;
$settings['locale']['cache'] = null;
$settings['twig']['cache_enabled'] = false;
