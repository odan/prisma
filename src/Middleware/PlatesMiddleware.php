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
        $request = $request->withAttribute(static::ATTRIBUTE, $this->create($request));
        return $next($request, $response);
    }

    /**
     * Create instance
     *
     * @return Engine
     */
    public function create(Request $request)
    {
    $engine = new Engine($this->options['view_path'], null);

        // Add folder shortcut (assets::file.js)
        $engine->addFolder('assets', $this->options['assets_path']);
        $engine->addFolder('view', $this->options['view_path']);

        $session = $request->getAttribute(SessionMiddleware::ATTRIBUTE);
        $baseUrl = $request->getAttribute('base_url');

        // Register Asset extension
        $cacheOptions = array(
            // View base path
            'cachepath' => $this->options['cache_path'],
            // Create different hash for each language
            'cachekey' => $session->get('locale'),
            // Base Url for public cache directory
            'baseurl' => $baseUrl,
            // JavaScript and CSS compression
            'minify' => $this->options['minify']
        );
        $engine->loadExtension(new \Odan\Plates\Extension\AssetCache($cacheOptions));
        return $engine;
    }
}
