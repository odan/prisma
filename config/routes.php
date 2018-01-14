<?php

/**
 * Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */

// Default page
$app->get('/', 'App\Controller\HomeController:indexAction')->setName('root');

// Json request
$app->post('/index/load', 'App\Controller\HomeController:loadAction');

$app->any('/ping', 'App\Controller\HomeController:pingAction')->setArgument('_auth', false)->setArgument('_csrf', false);

// Login
// No auth check for this actions
// Option: _auth = false (no authentication and authorization)
$app->post('/login', 'App\Controller\LoginController:loginSubmitAction')->setArgument('_auth', false);
$app->get('/login', 'App\Controller\LoginController:loginAction')->setArgument('_auth', false)->setName('login');
$app->get('/logout', 'App\Controller\LoginController:logoutAction')->setArgument('_auth', false);

// Users
$app->get('/users', 'App\Controller\UserController:indexAction');

// This route will only match if {id} is numeric
$app->get('/users/{id:[0-9]+}', 'App\Controller\UserController:editAction')->setName('users.edit');

// Sub-Resource
$app->get('/users/{id:[0-9]+}/reviews', 'App\Controller\UserController:reviewAction');
