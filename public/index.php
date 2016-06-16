<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Helper/util.php';

use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

call_user_func(function () {
    // Invoke the relay queue with a request and response.
    $runner = new Relay\Runner(read(__DIR__ . '/../src/Config/middleware.php'));
    $response = $runner(ServerRequestFactory::fromGlobals(), new Response());

    // Output response
    $emitter = new SapiEmitter();
    return $emitter->emit($response);
});
