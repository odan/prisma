<?php

$config = config();

// Defaults
$config->load(__DIR__ . '/default.php');

// Load environment configuration
$environment = [];
if (file_exists(__DIR__ . '/../../env.php')) {
    $environment = $config->read(__DIR__ . '/../../env.php');
}
if (file_exists(__DIR__ . '/env.php')) {
    $environment = $config->read(__DIR__ . '/env.php');
}

if (isset($environment['env'])) {
    $config->load(__DIR__ . '/' . $environment['env'] . '.php');
}

$config->read(__DIR__ . '/middleware.php');
$config->read(__DIR__ . '/routes.php');

if ($environment) {
    $config->import($environment);
}

return $config;
