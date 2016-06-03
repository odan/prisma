<?php

use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

// Invoke the relay queue with a request and response.
$runner = new Relay\Runner(include __DIR__ . '/middleware.php');
$response = $runner(ServerRequestFactory::fromGlobals(), new Response());

// Output response
$emitter = new SapiEmitter();
$emitter->emit($response);
