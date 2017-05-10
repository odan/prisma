<?php

require_once __DIR__ . '/../config/bootstrap.php';

call_user_func(function () {
    // Invoke the relay queue with a request and response.
    $runner = new Relay\Runner(config()->get('middleware'));
    $response = $runner(request(), response());

    // Output response
    $emitter = new \Zend\Diactoros\Response\SapiEmitter();
    return $emitter->emit($response);
});
