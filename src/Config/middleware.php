<?php

// Create a queue array of middleware callables
$queue = [];

// Error handler
$queue[] = new \App\Middleware\ExceptionMiddleware(['verbose' => true, 'logger' => null]);

// Startup
$config = read(__DIR__ . '/config.php');
$queue[] = new \App\Middleware\StartupMiddleware($config);

// Router
$routes = read(__DIR__ . '/routes.php');
$queue[] = new \App\Middleware\FastRouteMiddleware(['routes' => $routes]);

return $queue;
