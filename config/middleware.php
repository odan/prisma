<?php

use App\Service\User\AuthService;
use Odan\Slim\Csrf\CsrfMiddleware;
use Odan\Slim\Session\Session;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

$container = $app->getContainer();

// Authentication middleware
$app->add(function (Request $request, Response $response, $next) {
    /* @var \Slim\Route $route */
    $route = $request->getAttribute('route');
    $request->getHeader('');

    if (!$route) {
        return $next($request, $response);
    }
    $auth = $route->getArgument('_auth', true);

    /* @var \App\Service\User\AuthService $user */
    $user = $this->get(AuthService::class);
    if ($auth === true && !$user->hasIdentity()) {
        // Redirect to login page

        /* @var \Slim\Router $router */
        $router = $this->get('router');
        $uri = $router->pathFor('login');

        return $response->withRedirect($uri);
    }

    return $next($request, $response);
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
    /* @var Container $this */
    $localisation = $this->get(\App\Service\User\Locale::class);

    // Get user language
    $locale = $localisation->getLocale();
    $domain = $localisation->getDomain();

    // Default language
    if (empty($locale)) {
        $locale = 'en_US';
        $domain = 'messages';
    }

    // Set language
    $localisation->setLanguage($locale, $domain);

    return $next($request, $response);
});

// Csrf protection middleware
$app->add(function (Request $request, Response $response, $next) {
    if (php_sapi_name() === 'cli') {
        return $next($request, $response);
    }

    /* @var Container $this */

    /* @var \Slim\Route $route */
    $route = $request->getAttribute('route');

    if (!$route) {
        return $next($request, $response);
    }
    $isProtected = $route->getArgument('_csrf', true);

    if (!$isProtected) {
        return $next($request, $response);
    }

    $csrf = $this->get(CsrfMiddleware::class);

    return $csrf->__invoke($request, $response, $next);
});

// CORS preflight middleware
$app->add(function (Request $request, Response $response, $next) {
    if ($request->getMethod() !== 'OPTIONS' || php_sapi_name() === 'cli') {
        return $next($request, $response);
    }

    $response = $next($request, $response);

    $response = $response->withHeader('Access-Control-Allow-Origin', '*');
    $response = $response->withHeader('Access-Control-Allow-Methods', $request->getHeaderLine('Access-Control-Request-Method'));
    $response = $response->withHeader('Access-Control-Allow-Headers', $request->getHeaderLine('Access-Control-Request-Headers'));

    return $response;
});

// Session middleware
$app->add(function (Request $request, Response $response, $next) {
    /* @var Container $this */
    $session = $this->get(Session::class);
    $session->start();
    $response = $next($request, $response);
    $session->save();

    return $response;
});
