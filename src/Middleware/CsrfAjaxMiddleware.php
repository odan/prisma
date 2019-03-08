<?php

namespace App\Middleware;

use Slim\Csrf\Guard;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * CSRF Ajax protection middleware.
 */
final class CsrfAjaxMiddleware
{
    /**
     * @var Guard
     */
    private $guard;

    /**
     * Constructor.
     *
     * @param Guard $guard the slim guard
     */
    public function __construct(Guard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Invoke.
     *
     * @param Request $request the request
     * @param Response $response the response
     * @param callable $next next callback
     *
     * @return Response the response
     */
    public function __invoke(Request $request, Response $response, $next): Response
    {
        if (!$headers = $request->getHeader('X-CSRF-Token')) {
            return $next($request, $response);
        }

        if (!in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
            return $next($request, $response);
        }

        $body = $request->getParsedBody();
        if (!is_array($body)) {
            return $next($request, $response);
        }

        // Append token to parsed body
        list($tokenName, $tokenValue) = explode('_', (string)$headers[0]);

        $body += [
            $this->guard->getTokenNameKey() => $tokenName,
            $this->guard->getTokenValueKey() => $tokenValue,
        ];

        $request = $request->withParsedBody($body);

        return $next($request, $response);
    }
}
