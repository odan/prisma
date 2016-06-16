<?php

namespace App\Middleware;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * SessionMiddleware
 */
class SessionMiddleware
{

    const ATTRIBUTE = 'session';

    /**
     * Settings
     *
     * @var array
     */
    protected $config;

    /**
     * Set the Middleware instance and options.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
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
        $request = $request->withAttribute(static::ATTRIBUTE, $this->create());
        return $next($request, $response);
    }

    /**
     * Create instance
     *
     * @return Session
     */
    public function create()
    {
        if (php_sapi_name() === 'cli') {
            // In cli-mode
            $storage = new MockArraySessionStorage(new NullSessionHandler());
            $session = new Session($storage);
        } else {
            // Not in cli-mode
            $storage = new NativeSessionStorage($this->config, new NativeFileSessionHandler());
            $session = new Session($storage);
            $session->start();
        }
        return $session;
    }
}
