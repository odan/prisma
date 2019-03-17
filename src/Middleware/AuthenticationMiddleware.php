<?php

namespace App\Middleware;

use App\Domain\User\Auth;
use Interop\Container\Exception\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Container;
use Slim\Route;
use Slim\Router;

/**
 * Middleware.
 */
class AuthenticationMiddleware
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param Container $container The container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Middleware invokable class to verify logged-in user.
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
        /** @var Route $route */
        $route = $request->getAttribute('route');

        if (empty($route)) {
            return $next($request, $response);
        }

        /** @var Auth $user */
        $user = $this->container->get(Auth::class);

        if (!$user->check()) {
            // Redirect to login page

            /** @var Router $router */
            $router = $this->container->get('router');
            $uri = $router->pathFor('login');

            return $response->withHeader('Location', $uri);
        }

        return $next($request, $response);
    }
}
