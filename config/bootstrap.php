<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Instantiate the slim application
app();

require_once  __DIR__ . '/container.php';

// Register middleware
require_once __DIR__ . '/middleware.php';

// Register routes
require_once __DIR__ . '/routes.php';
