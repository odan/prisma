<?php

$router = router();
$config = config();

// HTTP
$router->middleware(new \App\Middleware\HttpMiddleware());

// Session
$router->middleware(new \App\Middleware\SessionMiddleware($config->get('session')));

// Translator
$router->middleware(new \App\Middleware\TranslatorMiddleware($config->export()));

// Compression
$router->middleware(new \App\Middleware\CompressMiddleware());

return $router;
