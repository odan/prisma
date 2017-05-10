<?php

$config = config();

// Create a queue array of middleware callables
$queue = [];

// Logger
//$queue[] = new \App\Middleware\LoggerMiddleware($config->get('log'));

// Error handler
$queue[] = new \App\Middleware\ExceptionMiddleware(['verbose' => true]);

// HTTP
$queue[] = new \App\Middleware\HttpMiddleware();

// Session
$queue[] = new \App\Middleware\SessionMiddleware($config->get('session'));

// Translator
$queue[] = new \App\Middleware\TranslatorMiddleware($config->export());

// Router
$queue[] = new \App\Middleware\FastRouteMiddleware(['routes' => $config->get('routes')]);

// Compression
$queue[] = new \App\Middleware\CompressMiddleware();

return $queue;
