<?php

namespace App\Middleware;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * AuthenticationMiddleware
 */
class AuthenticationMiddleware
{
    /**
     * Authenticator
     *
     * @var callable
     */
    protected $authenticator;

    /**
     * allowedRoutes
     *
     * @var array
     */
    protected $allowedRoutes;

    /**
     * Request
     *
     * @var Request
     */
    protected $request;

    /**
     * Response
     *
     * @var Request
     */
    protected $response;

    /**
     * Set the Middleware instance and options.
     *
     * @param callable $authenticator
     */
    public function setAuthenticator(callable $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * Set the Middleware instance and options.
     *
     * @param array $routes
     */
    public function setAllowedRoutes(array $routes)
    {
        $this->allowedRoutes = $routes;
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
        call_user_func($this->authenticator, $request, $response, [$this->allowedRoutes]);
        return $next($request, $response);
    }
}
