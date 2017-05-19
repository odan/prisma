<?php

use League\Route\Http\Exception\UnauthorizedException;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequest as Request;

$router = router();

// Global strategy to be used by all routes.
$errorHandler = new \App\Middleware\HttpExceptionStrategy();
$errorHandler->setLogger(logger());

// Handle http errors
$errorHandler->on(UnauthorizedException::class, function () {
    // Redirect to login page
    return new RedirectResponse(baseurl('/login'));
});

$router->setStrategy($errorHandler);

// Default page
$router->map('GET', '/', function () {
    $ctrl = new App\Controller\IndexController();
    return $ctrl->indexPage();
});

$router->map('GET', '/index/load', function () {
    $ctrl = new App\Controller\IndexController();
    return $ctrl->load();
});

// Login
$router->map('GET', '/login', function () {
    // No auth check for this action
    container()->share('request', request()->withAttribute('_auth', false));
    $ctrl = new App\Controller\LoginController();
    return $ctrl->loginPage();
});

$router->map('POST', '/login', function () {
    container()->share('request', request()->withAttribute('_auth', false));
    $ctrl = new App\Controller\LoginController();
    return $ctrl->loginSubmit();
});

$router->map('GET', '/logout', function () {
    container()->share('request', request()->withAttribute('_auth', false));
    $ctrl = new App\Controller\LoginController();
    return $ctrl->logout();
});

// Users
$router->map('GET', '/users', function () {
    $ctrl = new App\Controller\UserController();
    return $ctrl->indexPage();
});

// this route will only match if {id} is numeric
$router->map('GET', '/users/{id:number}', function (Request $request, Response $response, array $args) {
    $ctrl = new App\Controller\UserController();
    return $ctrl->editPage($args);
});

// Sub-Resource
$router->map('GET', '/users/{id:number}/reviews', function (Request $request, Response $response, array $args) {
    $ctrl = new App\Controller\UserController();
    return $ctrl->reviewPage($args);
});
