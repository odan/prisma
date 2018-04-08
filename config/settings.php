<?php

// Defaults
require __DIR__ . '/default.php';

// Load environment configuration
if (file_exists(__DIR__ . '/../../env.php')) {
    require __DIR__ . '/../../env.php';
} elseif (file_exists(__DIR__ . '/env.php')) {
    require __DIR__ . '/env.php';
}

if (defined('APP_ENV')) {
    // integration
    $settings['env'] = APP_ENV;
}

if (isset($settings['env'])) {
    require __DIR__ . '/' . $settings['env'] . '.php';
}

return $settings;
