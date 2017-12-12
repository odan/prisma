<?php

use App\Service\User\AuthenticationService;
use Aura\Session\Session;
use Odan\Slim\Csrf\CsrfMiddleware;
use Slim\Container;
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

    /* @var \App\Service\User\AuthenticationService $user */
    $user = $this->get(AuthenticationService::class);
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
    $localization = $this->get(\App\Service\User\Localization::class);

    // Get user language
    $locale = $localization->getLocale();
    $domain = $localization->getDomain();

    // Default language
    if (empty($locale)) {
        $locale = 'en_US';
        $domain = 'messages';
    }

    // Set language
    $localization->setLanguage($locale, $domain);

    return $next($request, $response);
});

// Csrf protection middleware
$app->add(function (Request $request, Response $response, $next) {
    /* @var Container $this */
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
