<?php

namespace App\Middleware;

use App\Domain\User\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Route;
use Slim\Router;

/**
 * Middleware.
 */
class AuthenticationMiddleware
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * Constructor.
     *
     * @param Router $router
     * @param Auth $auth
     */
    public function __construct(Router $router, Auth $auth)
    {
        $this->router = $router;
        $this->auth = $auth;
    }

    /**
     * Middleware invokable class to verify logged-in user.
     *
     * @param  ServerRequestInterface $request PSR7 request
     * @param  ResponseInterface $response PSR7 response
     * @param  callable $next Next middleware
     *
     * @return ResponseInterface PSR7 response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next): ResponseInterface
    {
        /** @var Route $route */
        $route = $request->getAttribute('route');

        if (empty($route)) {
            return $next($request, $response);
        }

        if (!$this->auth->check()) {
            // Redirect to login page
            $uri = $this->router->pathFor('login');

            return $response->withHeader('Location', $uri);
        }

        return $next($request, $response);
    }
}
