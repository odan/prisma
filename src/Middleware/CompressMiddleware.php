<?php

namespace App\Middleware;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

/**
 * Body content compression (Gzip/deflate)
 */
class CompressMiddleware
{

    /**
     * Settings
     *
     * @var array
     */
    protected $settings;

    /**
     * Set the Middleware instance and options.
     *
     * @param array $settings
     */
    public function __construct($settings = array())
    {
        $this->settings = $settings;
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
        $response = $next($request, $response);
        return $this->compress($request, $response);
    }

    /**
     * Compress content.
     *
     * @return Response
     */
    public function compress(Request $request, Response $response)
    {
        $acceptEncoding = $request->getHeaderLine('accept-encoding');
        $encodings = array_flip($this->trimArray(explode(',', $acceptEncoding)));

        if (isset($encodings['gzip'])) {
            return $this->compressBody($response, 'gzip', 'gzcompress');
        }
        if (isset($encodings['deflate'])) {
            return $this->compressBody($response, 'deflate', 'gzdeflate');
        }
        return $response;
    }

    /**
     * Compress body content
     *
     * @param Response $response Response
     * @param string $encoding Encoding
     * @param string $method Method
     * @return Response Response
     */
    public function compressBody(Response $response, $encoding, $method)
    {
        $response = $response->withHeader('content-encoding', $encoding);

        // Thanks to Logan Bailey
        $content = $response->getBody()->__toString();
        $string = $method($content);

        // Replace the whole content
        $body = new Stream('php://temp', 'rw+');
        $body->write($string);
        return $response->withBody($body);
    }

    /**
     * Returns a trimmed array.
     *
     * @param array $array Array
     * @return array Array
     */
    protected function trimArray($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $val) {
                $array[$key] = $this->trimArray($val);
            }
            return $array;
        } else {
            if (is_string($array)) {
                $array = trim($array);
            }
            return $array;
        }
    }
}
