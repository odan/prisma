<?php

namespace App\Middleware;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * CakeDatabaseMiddleware
 */
class CakeDatabaseMiddleware
{

    /**
     * Attribute
     *
     * @var string Attribute
     */
    const ATTRIBUTE = 'database';

    /**
     * Settings
     *
     * @var array
     */
    protected $options;

    /**
     * Set the Middleware instance and options.
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->options = $options;
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
        // Add service to request object
        $request = $request->withAttribute(static::ATTRIBUTE, $this->create());
        return $next($request, $response);
    }

    /**
     * Create instance
     *
     * @return Connection
     */
    public function create()
    {
        return new Connection(['driver' => new Mysql($this->options)]);
    }
}
