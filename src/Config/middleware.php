<?php

// Create a queue array of middleware callables
$queue = [];

// Error handler
$queue[] = new \App\Middleware\ExceptionMiddleware(['verbose' => true, 'logger' => null]);

// Application
$queue[] = new \App\Middleware\AppMiddleware(read(__DIR__ . '/app.php'));

// Router
$queue[] = new \App\Middleware\FastRouteMiddleware(['routes' => read(__DIR__ . '/routes.php')]);

return $queue;
