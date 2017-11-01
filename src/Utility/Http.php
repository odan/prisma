<?php

namespace App\Utility;

use Slim\Http\Environment;
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
    private $request;

    /**
     * Environment
     *
     * @var Environment
     */
    private $environment;

    /**
     * Constructor
     *
     * @param Request $request The request
     */
    public function __construct(Request $request, Environment $environment)
    {
        $this->request = $request;
        $this->environment = $environment;
    }

    /**
     * Returns a URL rooted at the base url for all relative URLs in a document
     *
     * @param string $path the path
     * @return string base url for $internalUri
     */
    public function getBaseUrl($path)
    {
        $result = $this->getHostUrl() . $this->getBasePath($path);

        return $result;
    }

    /**
     * Returns a URL rooted at the base url for all relative URLs in a document
     *
     * @param string $path the path
     * @return string base url for $internalUri
     */
    public function getBasePath($path)
    {
        if ($this->environment->get('REAL_SCRIPT_NAME')) {
            $scriptName = $this->environment->get('REAL_SCRIPT_NAME');
        } else {
            $scriptName = $this->environment->get('SCRIPT_NAME');
        }

        $baseUri = dirname(dirname($scriptName));
        $result = str_replace('\\', '/', $baseUri) . $path;
        $result = str_replace('//', '/', $result);

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
        $server = $this->request->getServerParams();
        if (!empty($server['HTTPS'])) {
            return strtolower($server['HTTPS']) !== 'off';
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
        $path = strpos($path, '/') === 0 ? $path: '/' . $path;
        $path = $this->getBasePath($path);
        $query = $uri->getQuery();
        $result = $this->isSecure() ? 'https://' : 'http://';
        $result .= (empty($port)) ? $host . $path : $host . ":" . $port . $path;
        $result .= strlen($query) ? '?' . $query : '';

        return $result;
    }
}
