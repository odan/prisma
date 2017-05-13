<?php

$router = router();
$config = config();

// Session
$router->middleware(new \App\Middleware\SessionMiddleware($config->get('session')));

// Translator
$router->middleware(new \App\Middleware\TranslatorMiddleware($config->export()));

// Json
$router->middleware(new \App\Middleware\JsonMiddleware());

// Compression
$router->middleware(new \App\Middleware\CompressMiddleware());

return $router;
