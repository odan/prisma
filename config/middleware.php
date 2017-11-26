<?php

use App\Service\User\Authentication;
use Aura\Session\Session;
use Slim\Http\Request;
use Slim\Http\Response;

$app = app();
$container = $app->getContainer();

// Authentication middleware
$app->add(function (Request $request, Response $response, $next) use ($container) {
    /* @var \Slim\Route $route */
    $route = $request->getAttribute('route');

    if (!$route) {
        return $next($request, $response);
    }
    $auth = $route->getArgument('_auth', true);

    /* @var \App\Service\User\Authentication $user */
    $user = $container->get(Authentication::class);
    if ($auth === true && !$user->check()) {
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
    //Checks whether the request is secure or not.
    $server = $request->getServerParams();
    $secure = !empty($server['HTTPS']) && strtolower($server['HTTPS']) !== 'off';
    $request = $request->withAttribute('secure', $secure);

    $container->set('request', $request);
    return $next($request, $response);
});

// Language middleware
$app->add(function (Request $request, Response $response, $next) use ($container) {
    $user = $container->get(Authentication::class);
    $user->setLocale($user->getLocale());
    return $next($request, $response);
});

// Session middleware
$app->add(function (Request $request, Response $response, $next) use ($container) {
    $session = $container->get(Session::class);
    $response = $next($request, $response);
    $session->commit();
    return $response;
});
