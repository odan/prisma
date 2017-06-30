<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app = app();
$container = $app->getContainer();

// Authentication middleware
$app->add(function (Request $request, Response $response, $next) use ($container) {
    /* @var \Slim\Route $route */
    $route = $request->getAttribute('route');
    $auth = $route->getArgument('_auth', true);

    /* @var \App\Service\User\UserSession $user */
    $user = $container->get('user');
    if ($auth === true && !$user->isValid()) {
        // Redirect to login page

        /* @var \Slim\Router $router */
        $router = $this->get('router');
        $uri = $router->pathFor('login');
        return $response->withRedirect($uri);
    } else {
        return $next($request, $response);
    }
});

// Http middleware
$app->add(function (Request $request, Response $response, $next) use ($container) {
    $http = new \App\Utility\Http($request);
    $request = $request->withAttribute('url', $http->getUrl());
    $request = $request->withAttribute('baseUrl', $http->getBaseUrl('/'));
    $request = $request->withAttribute('hostUrl', $http->getHostUrl());
    $request = $request->withAttribute('secure', $http->isSecure());
    return $next($request, $response);
});

// Language middleware
$app->add(function (Request $request, Response $response, $next) use ($container) {
    /** @var \App\Service\User\UserSession $user */
    $user = $container->get('user');
    $user->setLocale($user->getLocale());
    return $next($request, $response);
});


// Session middleware
$app->add(function (Request $request, Response $response, $next) use ($container) {
    /* @var $session \Aura\Session\Session */
    $session = $container->get('session');
    $response = $next($request, $response);
    $session->commit();
    return $response;
});
