<?php

// Defaults
$config = include __DIR__ . '/application.php';

// Environment specific confifiguration
$envFile = [
    __DIR__ . '/../../environment.php',
    __DIR__ . '/environment.php'
];
foreach ($envFile as $envFile) {
    if (file_exists($envFile)) {
        $config = array_merge_recursive($config, include $envFile);
    }
}
if (isset($config['env']['name'])) {
    $config = array_merge_recursive($config, include $config['env']['name'] . '.php');
}

return $config;
