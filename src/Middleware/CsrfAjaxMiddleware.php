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
        if (!in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
            return $next($request, $response);
        }

        if ($headers = $request->getHeader('X-CSRF-Token')) {
            list($tokenName, $tokenValue) = explode('_', (string)$headers[0]);
            $body = $request->getParsedBody() ?: [];

            $keyPair = [
                $this->guard->getTokenNameKey() => $tokenName,
                $this->guard->getTokenValueKey() => $tokenValue,
            ];

            // Append token to parsed body
            $body += $keyPair;
            $request = $request->withParsedBody($body);
        }

        /* @var Response $response */
        return $next($request, $response);
    }
}
