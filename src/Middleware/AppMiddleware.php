<?php

namespace App\Middleware;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * Application service container middleware.
 */
class AppMiddleware
{

    const ATTRIBUTE = 'app';

    /**
     * Application
     *
     * @var mixed
     */
    protected $app;

    /**
     * Set the Middleware instance and options.
     *
     * @param mixed $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Invoke middleware.
     *
     * @param Request $request The request.
     * @param Response $response The response.
     * @param callable $next Callback to invoke the next middleware.
     * @return Response A response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        // Put service container to request object
        $request = $request->withAttribute(static::ATTRIBUTE, $this->app);

        return $next($request, $response);
    }

}
