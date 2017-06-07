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
$app->get('/', function (Request $request) {
    /* @var \App\Controller\IndexController $controller */
    $controller = $this->get(\App\Controller\IndexController::class);
    return $controller->indexPage($request);
});

// Json request
$app->get('/index/load', function (Request $request) {
    /* @var \App\Controller\IndexController $controller */
    $controller = $this->get(\App\Controller\IndexController::class);
    return $controller->load($request);
});

// Login
// No auth check for this actions
// Option: _auth = false (no authentication and authorization)
$app->post('/login', function (Request $request) {
    /* @var \App\Controller\LoginController $controller */
    $controller = $this->get(\App\Controller\LoginController::class);
    return $controller->loginSubmit($request);
})->setArgument('_auth', false);;

$app->get('/login', function (Request $request) {
    /* @var \App\Controller\LoginController $controller */
    $controller = $this->get(\App\Controller\LoginController::class);
    return $controller->loginPage($request);
})->setArgument('_auth', false);;

$app->get('/logout', function (Request $request) {
    /* @var \App\Controller\LoginController $controller */
    $controller = $this->get(\App\Controller\LoginController::class);
    return $controller->logout($request);
})->setArgument('_auth', false);;

// Users
$app->get('/users', function (Request $request) {
    /* @var \App\Controller\UserController $controller */
    $controller = $this->get(\App\Controller\UserController::class);
    return $controller->indexPage($request);
});

// This route will only match if {id} is numeric
$app->get('/users/{id:[0-9]+}', function (Request $request) {
    /* @var \App\Controller\UserController $controller */
    $controller = $this->get(\App\Controller\UserController::class);
    return $controller->editPage($request);
});

// Sub-Resource
$app->get('/users/{id:[0-9]+}/reviews', function (Request $request, Response $response, $args) {
    /* @var \App\Controller\UserController $controller */
    $controller = $this->get(\App\Controller\UserController::class);
    return $controller->reviewPage($request, $response, $args);
});
