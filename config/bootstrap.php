<?php

use Symfony\Component\Translation\Translator;

require_once __DIR__ . '/../vendor/autoload.php';

// Instantiate the app
$app = new \Slim\App(['settings' => require __DIR__ . '/../config/settings.php']);

// Set up dependencies
require __DIR__ . '/container.php';

// Register middleware
require __DIR__ . '/middleware.php';

// Register routes
require __DIR__ . '/routes.php';

// Set translator instance
__($app->getContainer()->get(Translator::class));

return $app;
