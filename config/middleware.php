<?php

// Global middleware

$app->add(\App\Middleware\CorsMiddleware::class);

/*
$container = $app->getContainer();

$app->add(function (Request $request, Response $response, $next) {
    return $next($request, $response);
});
*/