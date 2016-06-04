<?php

use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

/**
 * Main function
 *
 * @return null|int Description
 */
function main()
{
    // Invoke the relay queue with a request and response.
    $runner = new Relay\Runner(read(__DIR__ . '/middleware.php'));
    $response = $runner(ServerRequestFactory::fromGlobals(), new Response());

    // Output response
    $emitter = new SapiEmitter();
    return $emitter->emit($response);
}
