<?php

// Defaults
$config = read(__DIR__ . '/default.php');

// Load environment configuration
$environment = [];
if (file_exists(__DIR__ . '/../../env.php')) {
    $environment = read(__DIR__ . '/../../env.php');
}
if (file_exists(__DIR__ . '/env.php')) {
    $environment = read(__DIR__ . '/env.php');
}

if (defined('APP_ENV')) {
    // testing
    $environment['env'] = APP_ENV;
}

if (isset($environment['env'])) {
    $config = array_replace_recursive($config, read(__DIR__ . '/' . $environment['env'] . '.php'));
}

if ($environment) {
    $config = array_replace_recursive($config, $environment);
}

return $config;
