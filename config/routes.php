<?php

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use League\Route\Http\Exception\UnauthorizedException;
use Zend\Diactoros\Response\RedirectResponse;

$router = router();

// Global strategy to be used by all routes.
$errorHandler = new \App\Middleware\HttpExceptionStrategy();
$errorHandler->setLogger(logger());

// Handle http errors
$errorHandler->on(UnauthorizedException::class, function() {
    // Redirect to login page
    return new RedirectResponse(baseurl('/login'));
});

$router->setStrategy($errorHandler);

// Default page
$router->map('GET', '/', function (Request $request, Response $response) {
    $ctrl = new App\Controller\IndexController($request, $response);
    return $ctrl->indexPage();
});

$router->map('GET', '/index/load', function (Request $request, Response $response) {
    $ctrl = new App\Controller\IndexController($request, $response);
    return $ctrl->load();
});

// Login
$router->map('GET', '/login', function (Request $request, Response $response) {
    // No auth check for this action
    $request = $request->withAttribute('_auth', false);
    $ctrl = new App\Controller\LoginController($request, $response);
    return $ctrl->loginPage();
});

$router->map('POST', '/login', function (Request $request, Response $response) {
    $request = $request->withAttribute('_auth', false);
    $ctrl = new App\Controller\LoginController($request, $response);
    return $ctrl->loginSubmit();
});

$router->map('GET', '/logout', function (Request $request, Response $response) {
    $request = $request->withAttribute('_auth', false);
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


