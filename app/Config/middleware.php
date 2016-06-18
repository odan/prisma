<?php

$config = read(__DIR__ . '/config.php');

// Create a queue array of middleware callables
$queue = [];

// Logger
$queue[] = new \App\Middleware\LoggerMiddleware($config['log']);

// Error handler
$queue[] = new \App\Middleware\ExceptionMiddleware(['verbose' => true]);

// HTTP
$queue[] = new \App\Middleware\HttpMiddleware();

// Session
$queue[] = new \App\Middleware\SessionMiddleware($config['session']);

// Translator
$queue[] = new \App\Middleware\TranslatorMiddleware($config);

// View
$queue[] = new \App\Middleware\PlatesMiddleware($config['view']);

// Database
$queue[] = new \App\Middleware\CakeDatabaseMiddleware($config['db']);

// Application
$queue[] = new \App\Middleware\AppMiddleware($config);

// Router
$queue[] = new \App\Middleware\FastRouteMiddleware($config['router']);

// Compression
$queue[] = new \App\Middleware\CompressMiddleware();

return $queue;
