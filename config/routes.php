<?php

/**
 * Define the Slim application routes
 */

// Default page
$app->get('/', \App\Action\HomeIndexAction::class)->setName('root');

// Json request
$app->post('/index/load', \App\Action\HomeLoadAction::class);

$app->any('/ping', \App\Action\HomePingAction::class)->setArgument('_auth', false)->setArgument('_csrf', false);

// Login
// No auth check for this actions
// Option: _auth = false (no authentication and authorization)
$app->post('/login', \App\Action\LoginSubmitAction::class)->setArgument('_auth', false);
$app->get('/login', \App\Action\LoginIndexAction::class)->setArgument('_auth', false)->setName('login');
$app->get('/logout', \App\Action\LoginLogoutAction::class)->setArgument('_auth', false);

// Users
$app->get('/users', \App\Action\UserIndexAction::class);

// This route will only match if {id} is numeric
$app->get('/users/{id:[0-9]+}', \App\Action\UserEditAction::class)->setName('users.edit');

// Sub-Resource
$app->get('/users/{id:[0-9]+}/reviews', \App\Action\UserReviewAction::class);
