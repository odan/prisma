<?php

// Add routes: httpMethod, route, handler
$routes = [];

// Default page
$routes[] = ['GET', '/', function($request, $response) {
    $ctrl = new App\Controller\UserController($request, $response);
    return $ctrl->indexPage();
}];

// JSON middleware for all Json requests
$routes[] = ['POST', '/json', function($request, $response, $next) {
    $middleware = new App\Middleware\JsonRpcMiddleware();
    return $middleware->__invoke($request, $response, $next);
}];

// Login
$routes[] = ['GET', '/login', function($request, $response) {
    $ctrl = new App\Controller\LoginController($request, $response);
    return $ctrl->loginPage();
}];

$routes[] = ['POST', '/login', function($request, $response) {
    $ctrl = new App\Controller\LoginController($request, $response);
    return $ctrl->loginSubmit();
}];

$routes[] = ['GET', '/logout', function($request, $response) {
    $ctrl = new App\Controller\LoginController($request, $response);
    return $ctrl->logout();
}];

// Controller action
$routes[] = ['GET', '/users', function($request, $response) {
    $ctrl = new App\Controller\UserController($request, $response);
    return $ctrl->indexPage();
}];

// {id} must be a number (\d+)
$routes[] = ['GET', '/users/{id:\d+}', function($request, $response) {
    $ctrl = new App\Controller\UserController($request, $response);
    return $ctrl->editPage();
}];

// Sub-Resource
$routes[] = ['GET', '/users/{id:\d+}/reviews', function ($request, $response) {
    $ctrl = new App\Controller\UserController($request, $response);
    return $ctrl->reviewPage();
}];

//
// Whitelist with actions that require no authentication and authorization
//
/* $noAuth = [
    'App\Controller\LoginController->login',
    'App\Controller\LoginController->loginSubmit',
    'App\Controller\LoginController->logout',
];*/

//
// Event listener for checking authorization
//
/*$events = [
    'before.action' => '\App\Service\User\Authentication::check'
];*/

return $routes;
