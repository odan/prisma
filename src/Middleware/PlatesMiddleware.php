<?php

namespace App\Middleware;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use League\Plates\Engine;

/**
 * PlatesMiddleware
 */
class PlatesMiddleware
{

    /**
     * Attribute
     *
     * @var string Attribute
     */
    const ATTRIBUTE = 'view';

    /**
     * Settings
     *
     * @var array
     */
    protected $options;

    /**
     * Set the Middleware instance and options.
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->options = $options;
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
        $engine = new Engine($this->options['view_path'], null);

        // Add folder shortcut (assets::file.js)
        $engine->addFolder('assets', $this->options['assets_path']);
        $engine->addFolder('view', $this->options['view_path']);

        $session = $request->getAttribute(SessionMiddleware::ATTRIBUTE);
        // Register Asset extension
        $cacheOptions = array(
            // View base path
            'cachepath' => $this->options['view_path'],
            // Create different hash for each language
            'cachekey' => '', $session->get('locale'),
            // Base Url for public cache directory
            'baseurl' => '', // $this->http->getBaseUrl('/'),
            // JavaScript and CSS compression
            'minify' => $this->options['minify']
        );
        $engine->loadExtension(new \Odan\Plates\Extension\AssetCache($cacheOptions));

        // Put service container to request object
        $request = $request->withAttribute(static::ATTRIBUTE, $engine);

        return $next($request, $response);
    }
}
