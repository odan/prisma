<?php

namespace App\Middleware;

use App\Container\AppContainer;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * Application service container middleware.
 */
class AppMiddleware
{

    const ATTRIBUTE = 'app';

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
        // Add service to request object
        $request = $request->withAttribute(static::ATTRIBUTE, $this->create($request));
        return $next($request, $response);
    }

    /**
     * Create instance
     *
     * @return AppContainer
     */
    public function create(Request $request)
    {
        $app = new \App\Container\AppContainer();
        $app->config = $this->config;
        $app->logger = $request->getAttribute(LoggerMiddleware::ATTRIBUTE);
        $app->session = $request->getAttribute(SessionMiddleware::ATTRIBUTE);
        $app->translator = $request->getAttribute(TranslatorMiddleware::ATTRIBUTE);
        $app->db = $request->getAttribute(CakeDatabaseMiddleware::ATTRIBUTE);
        $app->view = $request->getAttribute(PlatesMiddleware::ATTRIBUTE);
        $app->http = $request->getAttribute(HttpMiddleware::ATTRIBUTE);
        $app->user = new \App\Service\User\UserSession($app);
        $app->user->setLocale($app->user->getLocale());
        return $app;
    }
}
