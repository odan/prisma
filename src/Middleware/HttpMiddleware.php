<?php

namespace App\Middleware;

use App\Helper\Http;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

/**
 * Request represents an HTTP request
 */
class HttpMiddleware
{

    /**
     * Attribute
     */
    const ATTRIBUTE = 'http';

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
        $http = $this->create($request, $response);

        $basePath = $http->getBasePath();
        $hostUrl = $http->getBaseUri();
        $baseUri = $http->getHostUrl();
        $baseUrl = $baseUri . $hostUrl . '/';

        $request = $request->withAttribute('base_path', $basePath);
        $request = $request->withAttribute('base_uri', $baseUri);
        $request = $request->withAttribute('host_url', $hostUrl);
        $request = $request->withAttribute('base_url', $baseUrl);

        // Put service container to request object
        $request = $request->withAttribute(static::ATTRIBUTE, $http);

        return $next($request, $response);
    }

    /**
     * Create instance
     *
     * @param ServerRequest $request
     * @param Response $response
     * @return Http
     */
    public function create(Request $request, Response $response)
    {
        return new Http($request, $response);
    }
}
