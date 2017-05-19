<?php

namespace App\Util;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Http utils
 */
class Http
{

    /**
     * Request
     *
     * @var Request
     */
    public $request;

    /**
     * Response
     *
     * @var Response
     */
    public $response;

    /**
     * Server
     *
     * @var array
     */
    public $server;

    /**
     * Constructor
     *
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->server = $this->request->getServerParams();
    }

    /**
     * Get GET parameter.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $query = $this->request->getQueryParams();
        $result = isset($query[$key]) ? $query[$key] : $default;
        return $result;
    }

    /**
     * Get POST parameter
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function post($key, $default = null)
    {
        $post = $this->request->getParsedBody();
        $result = isset($post[$key]) ? $post[$key] : $default;
        return $result;
    }

    /**
     * Returns the url path leading up to the current script.
     * Used to make the webapp portable to other locations.
     *
     * @return string uri
     */
    public function getBasePath()
    {
        // Get URI from URL
        $uri = $this->request->getUri()->getPath();

        // Detect and remove sub-folder from URI
        $scriptName = $this->server['SCRIPT_NAME'];

        if (isset($scriptName)) {
            $dirName = dirname($scriptName);
            $dirName = dirname($dirName);
            $len = strlen($dirName);
            if ($len > 0 && $dirName != '/') {
                $uri = substr($uri, $len);
            }
        }
        return $uri;
    }

    /**
     * Set base path.
     *
     * @return Request
     */
    public function withBasePath()
    {
        $basePath = $this->getBasePath();
        $uri = $this->request->getUri();
        $uri = $uri->withPath($basePath);
        $request = $this->request->withUri($uri);
        return $request;
    }

    /**
     * Returns a URL rooted at the base url for all relative URLs in a document
     *
     * @param string $internalUri the route
     * @param boolean $asAbsoluteUrl return absolute or relative url
     * @return string base url for $internalUri
     */
    public function getBaseUrl($internalUri, $asAbsoluteUrl = true)
    {
        $dirName = $this->getBaseUri();
        $result = $dirName . '/' . ltrim($internalUri, '/');
        $result = str_replace('//', '/', $result);

        if ($asAbsoluteUrl === true) {
            $result = $this->getUrl() . $result;
        }
        return $result;
    }

    /**
     * Get base url
     *
     * @return string
     */
    public function getBaseUri()
    {
        $scriptName = $this->server['SCRIPT_NAME'];
        $dirName = str_replace('\\', '', dirname($scriptName));
        $dirName = dirname($dirName);
        return $dirName;
    }

    /**
     * Returns current url
     *
     * @return string
     */
    public function getUrl()
    {
        $uri = $this->request->getUri();
        $host = $uri->getHost();
        $port = $uri->getPort();
        $path = $uri->getPath();
        $query = $uri->getQuery();
        $result = $this->isSecure() ? 'https://' : 'http://';
        $result .= (empty($port)) ? $host .$path : $host . ":" . $port . $path;
        $result .= strlen($query) ? '?' . $query : '';
        return $result;
    }

    /**
     * Checks whether the request is secure or not.
     *
     * This method can read the client protocol from the "X-Forwarded-Proto" header
     * when trusted proxies were set via "setTrustedProxies()".
     *
     * The "X-Forwarded-Proto" header must contain the protocol: "https" or "http".
     *
     * If your reverse proxy uses a different header name than "X-Forwarded-Proto"
     * ("SSL_HTTPS" for instance), configure it via "setTrustedHeaderName()" with
     * the "client-proto" key.
     *
     * @return bool
     */
    public function isSecure()
    {
        if (!empty($this->server['HTTPS'])) {
            return strtolower($this->server['HTTPS']) !== 'off';
        }
        return false;
    }

    /**
     * Returns true if a JSON request has been received.
     *
     * @param Request $request Request
     * @return bool Status
     */
    public function isJson(Request $request)
    {
        $type = $request->getHeader('content-type');
        return !empty($type[0]) && (strpos($type[0], 'application/json') !== false);
    }

    /**
     * Returns true if runing from localhost
     *
     * @return bool
     */
    public function isLocalhost()
    {
        $ipAddress = $this->getIp();
        return $ipAddress === '127.0.0.1' || $ipAddress === '::1';
    }

    /**
     * Find out the client's IP address from the headers available to us.
     * Inspired by akrabat/rka-ip-address-middleware.
     *
     * @return string
     */
    public function getIp()
    {
        $ipAddress = null;
        if (isset($this->server['REMOTE_ADDR'])) {
            $ipAddress = $this->server['REMOTE_ADDR'];
        }
        return $ipAddress;
    }

    /**
     * Redirect to url
     *
     * @param string $url
     * @return RedirectResponse
     */
    public function redirect($url)
    {
        return new RedirectResponse($url);
    }
}
