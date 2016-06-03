<?php

namespace App\Middleware;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * Error handling middleware.
 *
 * Traps exceptions and converts them into a error page.
 */
class StartupMiddleware
{

    /**
     * Options
     *
     * @var array
     */
    protected $options = array();

    /**
     * Set the Middleware instance and options.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $default = [];
        $this->options = $options + $default;
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
        $container = new \App\Container\ServiceContainer();

        // Load services
        $container->config = $this->options;
        // @todo Load more services
        // 
        // Put service container to request object
        $request = $request->withAttribute('container', $container);

        return $next($request, $response);
    }

}
