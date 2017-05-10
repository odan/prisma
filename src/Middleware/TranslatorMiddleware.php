<?php

namespace App\Middleware;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * TranslatorMiddleware
 */
class TranslatorMiddleware
{

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
        set_locale('en_US', 'messages');
        return $next($request, $response);
    }

}
