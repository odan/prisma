<?php

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

$router = router();

// Global strategy to be used by all routes.
$errorHandler = new \App\Middleware\HttpExceptionStrategy();
$errorHandler->setLogger(logger());
$router->setStrategy($errorHandler);

// Default page
$router->map('GET', '/', function (Request $request, Response $response) {
    $ctrl = new App\Controller\IndexController($request, $response);
    return $ctrl->indexPage();
});

// JSON middleware for all Json requests (Route specific middleware)
$router->map('POST', '/json', function (Request $request, Response $response) {
    //throw new \League\Route\Http\Exception\ForbiddenException();
    return $response;
})->middleware(new App\Middleware\JsonRpcMiddleware());

// Login
$router->map('GET', '/login', function (Request $request, Response $response) {
    $ctrl = new App\Controller\LoginController($request, $response);
    return $ctrl->loginPage();
});

$router->map('POST', '/login', function (Request $request, Response $response) {
    $ctrl = new App\Controller\LoginController($request, $response);
    return $ctrl->loginSubmit();
});

$router->map('GET', '/logout', function (Request $request, Response $response) {
    $ctrl = new App\Controller\LoginController($request, $response);
    return $ctrl->logout();
});

// Users
$router->map('GET', '/users', function (Request $request, Response $response) {
    $ctrl = new App\Controller\UserController($request, $response);
    return $ctrl->indexPage();
});

// this route will only match if {id} is numeric
$router->map('GET', '/users/{id:number}', function (Request $request, Response $response, array $args) {
    $ctrl = new App\Controller\UserController($request, $response);
    return $ctrl->editPage($args);
});

// Sub-Resource
$router->map('GET', '/users/{id:number}/reviews', function (Request $request, Response $response, array $args) {
    $ctrl = new App\Controller\UserController($request, $response);
    return $ctrl->reviewPage($args);
});

return $router;

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

