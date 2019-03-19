<?php

// Define the Slim application routes.

use App\Middleware\AuthenticationMiddleware;
use App\Middleware\CsrfAjaxMiddleware;
use App\Middleware\LanguageMiddleware;
use App\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Csrf\Guard;

/* @var App $app */
$container = $app->getContainer();

$app->any('/ping', \App\Action\HomePingAction::class);

// Login, no auth check for this actions required
$app->group('/users', function () {
    $this->post('/login', \App\Action\UserLoginSubmitAction::class);
    $this->get('/login', \App\Action\UserLoginIndexAction::class)->setName('login');
    $this->get('/logout', \App\Action\UserLogoutAction::class);
})
    ->add($container->get(Guard::class))
    ->add($container->get(SessionMiddleware::class));

// Routes with authentication
$app->group('', function () {
    /* @var App $this */

    // Default page
    $this->get('/', \App\Action\HomeIndexAction::class)->setName('root');

    $this->get('/users', \App\Action\UserIndexAction::class);

    // This route will only match if {id} is numeric
    $this->get('/users/{id:[0-9]+}', \App\Action\UserEditAction::class)->setName('users.edit');

    // Sub-Resource
    $this->get('/users/{id:[0-9]+}/reviews', \App\Action\UserReviewAction::class);

    // Json request
    $this->post('/home/load', \App\Action\HomeLoadAction::class);
})
    ->add($container->get(LanguageMiddleware::class))
    ->add($container->get(AuthenticationMiddleware::class))
    ->add($container->get(Guard::class))
    ->add($container->get(CsrfAjaxMiddleware::class))
    ->add($container->get(SessionMiddleware::class));
