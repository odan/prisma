<?php

namespace App\Middleware;

use Interop\Container\Exception\ContainerException;
use Odan\Slim\Csrf\CsrfMiddleware;
use Odan\Slim\Session\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Container;

/**
 * Middleware.
 */
class SessionMiddleware
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Invoke middleware.
     *
     * @param  ServerRequestInterface $request PSR7 request
     * @param  ResponseInterface $response PSR7 response
     * @param  callable $next Next middleware
     *
     * @throws ContainerException
     *
     * @return ResponseInterface PSR7 response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next): ResponseInterface
    {
        $session = $this->container->get(Session::class);
        $session->start();

        if (PHP_SAPI === 'cli') {
            $response = $next($request, $response);
        } else {
            $csrfMiddleware = $this->container->get(CsrfMiddleware::class);
            $csrfMiddleware->setSessionId($session->getId());
            $response = $csrfMiddleware->__invoke($request, $response, $next);
        }

        $session->save();

        return $response;
    }
}
