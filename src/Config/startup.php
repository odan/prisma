<?php

// Defaults
$config = include __DIR__ . '/application.php';

// Load environment configuration
if (file_exists(__DIR__ . '/../../environment.php')) {
    $envConfig = include __DIR__ . '/../../environment.php';
}
if (file_exists(__DIR__ . '/environment.php')) {
    $envConfig = include __DIR__ . '/environment.php';
}

if (isset($config['env']['name'])) {
    $config = array_merge_recursive($config, include $config['env']['name'] . '.php');
}
$config = array_replace_recursive($config, $envConfig);

return $config;
