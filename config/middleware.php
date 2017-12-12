<?php

use App\Service\Auth\Authentication;
use Aura\Session\Session;
use Odan\Slim\Csrf\CsrfMiddleware;
use Slim\Http\Request;
use Slim\Http\Response;

$app = app();

// Authentication middleware
$app->add(function (Request $request, Response $response, $next) {
    /* @var \Slim\Route $route */
    $route = $request->getAttribute('route');

    if (!$route) {
        return $next($request, $response);
    }
    $auth = $route->getArgument('_auth', true);

    /* @var \App\Service\Auth\Authentication $user */
    $user = $this->get(Authentication::class);
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
$app->add(function (Request $request, Response $response, $next) {
    // Checks whether the request is secure or not.
    $server = $request->getServerParams();
    $secure = !empty($server['HTTPS']) && strtolower($server['HTTPS']) !== 'off';
    $request = $request->withAttribute('secure', $secure);

    return $next($request, $response);
});

// Language middleware
$app->add(function (Request $request, Response $response, $next) {
    $user = $this->get(Authentication::class);
    $user->setLocale($user->getLocale());
    return $next($request, $response);
});

// Csrf protection middleware
$app->add(function (Request $request, Response $response, $next) {
    /* @var \Slim\Container $this */
    $session = $this->get(Session::class);
    $csrfValue = $session->getCsrfToken()->getValue();
    $csrf = $this->get(CsrfMiddleware::class);
    $csrf->setToken($csrfValue);

    return $csrf->__invoke($request, $response, $next);
});

// Session middleware
$app->add(function (Request $request, Response $response, $next) {
    $session = $this->get(Session::class);
    $response = $next($request, $response);
    $session->commit();
    return $response;
});
