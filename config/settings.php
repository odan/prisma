<?php

// Defaults
$settings = read(__DIR__ . '/default.php');

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
    $settings = array_replace_recursive($settings, read(__DIR__ . '/' . $environment['env'] . '.php'));
}

if ($environment) {
    $settings = array_replace_recursive($settings, $environment);
}

return $settings;
