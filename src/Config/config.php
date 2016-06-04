<?php

use Cake\Utility\Hash;

// Defaults
$defaults = read(__DIR__ . '/application.php');

// Load environment configuration
if (file_exists(__DIR__ . '/../../environment.php')) {
    $environment = read(__DIR__ . '/../../environment.php');
}
if (file_exists(__DIR__ . '/environment.php')) {
    $environment = read(__DIR__ . '/environment.php');
}
if (isset($environment['env']['name'])) {
    $config = read(__DIR__ . '/' . $environment['env']['name'] . '.php');
}

return Hash::merge($defaults, $config, $environment);
