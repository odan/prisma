<?php

$config = [];

// Add routes: httpMethod, route, handler
$routes = [];

// Default page
$routes[] = ['GET', '/', 'App\Controller\IndexController->index'];

// JSON-RPC 2.0 middleware for all Json requests
$routes[] = ['POST', '/rpc', 'App\Middleware\JsonRpcMiddleware->__invoke'];

// Login
$routes[] = ['GET', '/login', 'App\Controller\LoginController->login'];
$routes[] = ['POST', '/login', 'App\Controller\LoginController->loginSubmit'];
$routes[] = ['GET', '/logout', 'App\Controller\LoginController->logout'];

// Controller action
// Object method call with Class->method
$routes[] = ['GET', '/users', 'App\Controller\UserController->index'];

// {id} must be a number (\d+)
$routes[] = ['GET', '/user/{id:\d+}', 'App\Controller\UserController->edit'];

//
// Whitelist with actions that require no authentication and authorization
//
$noAuth = [
    'App\Controller\LoginController->login',
    'App\Controller\LoginController->loginSubmit',
    'App\Controller\LoginController->logout',
];

//
// Event listener for checking authorization
//
$events = [
    'before.action' => '\App\Service\User\Authentication::check'
];

$config['router'] = array(
    'routes' => $routes,
    'noauth' => $noAuth,
    'events' => $events
);

return $config;
