<?php

namespace App\Util;

use Slim\Http\Request;

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
     * Server
     *
     * @var array
     */
    public $server;

    /**
     * Constructor
     *
     * @param Request $request The request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->server = $this->request->getServerParams();
    }

    /**
     * Get GET parameter.
     *
     * @param string $key Key
     * @param mixed $default Default value
     * @return mixed Value
     */
    public function get($key, $default = null)
    {
        $query = $this->request->getQueryParams();
        return isset($query[$key]) ? $query[$key] : $default;
    }

    /**
     * Get POST parameter
     *
     * @param string $key Key
     * @param mixed $default Default value
     * @return mixed Value
     */
    public function post($key, $default = null)
    {
        $post = $this->request->getParsedBody();
        return isset($post[$key]) ? $post[$key] : $default;
    }

    /**
     * Returns a URL rooted at the base url for all relative URLs in a document
     *
     * @param string $internalUri the route
     * @param bool $asAbsoluteUrl return absolute or relative url
     * @return string base url for $internalUri
     */
    public function getBaseUrl($internalUri, $asAbsoluteUrl = true)
    {
        $result = $internalUri;
        if ($asAbsoluteUrl === true) {
            $result = $this->getHostUrl() . $result;
        }
        return $result;
    }

    /**
     * Returns current host url (without path and query)
     *
     * @return string
     */
    public function getHostUrl()
    {
        $uri = $this->request->getUri();
        $host = $uri->getHost();
        $port = $uri->getPort();
        $result = $this->isSecure() ? 'https://' : 'http://';
        $result .= (empty($port)) ? $host : $host . ":" . $port;
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
        $result .= (empty($port)) ? $host . $path : $host . ":" . $port . $path;
        $result .= strlen($query) ? '?' . $query : '';
        return $result;
    }
}
