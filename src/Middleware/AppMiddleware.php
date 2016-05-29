<?php

namespace App\Middleware;

use Exception;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

/**
 * Error handling middleware.
 *
 * Traps exceptions and converts them into a error page.
 */
class AppMiddleware
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
        // @todo
        // - Load config and env. files
        // - Put config and services to $request
        $app = new \stdClass();
        $request = $request->withAttribute('app', $app);

        return $next($request, $response);
    }
}
