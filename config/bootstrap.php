<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Instantiate the app
$app = new \Slim\App(['settings' => read(__DIR__ . '/../config/config.php')]);

// Set instance
app($app);

// Set up dependencies
require  __DIR__ . '/container.php';

// Register middleware
require __DIR__ . '/middleware.php';

// Register routes
require __DIR__ . '/routes.php';

return $app;
