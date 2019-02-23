<?php

// Global middleware

$app->add(\App\Middleware\CorsMiddleware::class);
$app->add(\App\Middleware\PhpErrorMiddleware::class);

/*
$container = $app->getContainer();

$app->add(function (Request $request, Response $response, $next) {
    return $next($request, $response);
});
*/
