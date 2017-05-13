<?php

namespace App\Middleware;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * JSON Middleware
 */
class JsonMiddleware
{
    /**
     * Attribute
     *
     * @var string Attribute
     */
    const ATTRIBUTE = 'data';

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
        if (!$this->isJson($request)) {
            return $next($request, $response);
        }

        $jsonRequest = $this->getJsonData($request);

        // Add json request
        $request = $request->withAttribute(static::ATTRIBUTE, $jsonRequest);

        $response = $next($request, $response);

        // Disable browser cache
        $response = $this->getResponseWithHeader($response);

        return $response;
    }

    /**
     * Get JSON request as array
     *
     * @param Request $request
     * @return array Data
     */
    protected function getJsonData(Request $request)
    {
        $requestContent = $request->getBody()->__toString();
        return json_decode($requestContent, true);
    }

    /**
     * Returns true if a JSON request has been received.
     *
     * @param Request $request Request
     * @return bool Status
     */
    protected function isJson(Request $request)
    {
        $type = $request->getHeader('content-type');
        return !empty($type[0]) && (strpos($type[0], 'application/json') !== false);
    }

    /**
     * Update response header.
     *
     * @param Response $response
     * @return Response
     */
    protected function getResponseWithHeader(Response $response)
    {
        $response = $response->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response = $response->withHeader('Pragma', 'no-cache');
        $response = $response->withHeader('Expires', '0');
        return $response;
    }
}
