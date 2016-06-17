<?php

// Defaults
$default = read(__DIR__ . '/default.php');

// Load environment configuration
$environment = [];
if (file_exists(__DIR__ . '/../../env.php')) {
    $environment = read(__DIR__ . '/../../env.php');
}
if (file_exists(__DIR__ . '/env.php')) {
    $environment = read(__DIR__ . '/env.php');
}
$config = [];
if (isset($environment['env']['name'])) {
    $config = read(__DIR__ . '/' . $environment['env']['name'] . '.php');
}

return array_replace_recursive($default, $config, $environment);
