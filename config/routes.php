<?php

/**
 * Define the Slim application routes.
 */
use Slim\App;

/* @var App $app */

// Default page
$app->get('/', \App\Action\HomeIndexAction::class)->setName('root');

// Json request
$app->post('/home/load', \App\Action\HomeLoadAction::class);

$app->any('/ping', \App\Action\HomePingAction::class)->setArgument('_auth', false)->setArgument('_csrf', false);

$app->group('/users', function () {
    /* @var App $this */

    // Login
    // No auth check for this actions
    // Option: _auth = false (no authentication and authorization)
    $this->post('/login', \App\Action\UserLoginSubmitAction::class)->setArgument('_auth', false);
    $this->get('/login', \App\Action\UserLoginIndexAction::class)->setArgument('_auth', false)->setName('login');
    $this->get('/logout', \App\Action\UserLoginLogoutAction::class)->setArgument('_auth', false);

    // Users
    $this->get('', \App\Action\UserIndexAction::class);

    // This route will only match if {id} is numeric
    $this->get('/{id:[0-9]+}', \App\Action\UserEditAction::class)->setName('users.edit');

    // Sub-Resource
    $this->get('/{id:[0-9]+}/reviews', \App\Action\UserReviewAction::class);
});
