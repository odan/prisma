<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app = app();
$container = $app->getContainer();

// Session middleware
$app->add(function (Request $request, Response $response, $next) use ($container) {
    /* @var $session \Aura\Session\Session */
    $session = $container->get('session');
    $response = $next($request, $response);
    $session->commit();
    return $response;
});

