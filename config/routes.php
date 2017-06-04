<?php

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
$app->get('/', 'App\Controller\IndexController:indexPage');

// Json request
$app->get('/index/load', 'App\Controller\IndexController:load');

// Login
// No auth check for this actions
// Option: _auth = false (no authentication and authorization)
$app->post('/login', '\App\Controller\LoginController:loginSubmit')->setArgument('_auth', false);
$app->get('/login', '\App\Controller\LoginController:loginPage')->setArgument('_auth', false);
$app->get('/logout', '\App\Controller\LoginController:logout')->setArgument('_auth', false);

// Users
$app->get('/users', '\App\Controller\UserController:indexPage');

// this route will only match if {id} is numeric
$app->get('/users/{id:[0-9]+}', '\App\Controller\UserController:editPage');

// Sub-Resource
$app->get('/users/{id:[0-9]+}/reviews', '\App\Controller\UserController:reviewPage');

$app->get('/foo', '\App\Controller\FooController:foo');
$app->get('/archive/{month:[0-9]+}', '\App\Controller\FooController:showArchive');
