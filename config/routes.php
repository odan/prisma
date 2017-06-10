<?php

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */
$app = app();

// Default page
$app->get('/', function (Request $request, Response $response) {
    /* @var \App\Controller\HomeController $controller */
    $controller = $this->get(\App\Controller\HomeController::class);
    return $controller->indexPage($request, $response);
});

// Json request
$app->post('/index/load', function (Request $request, Response $response) {
    /* @var \App\Controller\HomeController $controller */
    $controller = $this->get(\App\Controller\HomeController::class);
    return $controller->load($request, $response);
});

// Login
// No auth check for this actions
// Option: _auth = false (no authentication and authorization)
$app->post('/login', function (Request $request, Response $response) {
    /* @var \App\Controller\LoginController $controller */
    $controller = $this->get(\App\Controller\LoginController::class);
    return $controller->loginSubmit($request, $response);
})->setArgument('_auth', false);

$app->get('/login', function (Request $request, Response $response) {
    /* @var \App\Controller\LoginController $controller */
    $controller = $this->get(\App\Controller\LoginController::class);
    return $controller->loginPage($request, $response);
})->setArgument('_auth', false)->setName('login');

$app->get('/logout', function (Request $request, Response $response) {
    /* @var \App\Controller\LoginController $controller */
    $controller = $this->get(\App\Controller\LoginController::class);
    return $controller->logout($request, $response);
})->setArgument('_auth', false);

// Users
$app->get('/users', function (Request $request, Response $response) {
    /* @var \App\Controller\UserController $controller */
    $controller = $this->get(\App\Controller\UserController::class);
    return $controller->indexPage($request, $response);
});

// This route will only match if {id} is numeric
$app->get('/users/{id:[0-9]+}', function (Request $request, Response $response) {
    /* @var \App\Controller\UserController $controller */
    $controller = $this->get(\App\Controller\UserController::class);
    return $controller->editPage($request, $response);
});

// Sub-Resource
$app->get('/users/{id:[0-9]+}/reviews', function (Request $request, Response $response, $args) {
    /* @var \App\Controller\UserController $controller */
    $controller = $this->get(\App\Controller\UserController::class);
    return $controller->reviewPage($request, $response, $args);
});
