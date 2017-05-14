<?php

/**
 * Services and helper functions
 */

use Aura\Session\Session;
use Aura\Session\SessionFactory;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use League\Container\Container;
use League\Route\RouteCollection;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

/**
 * Dependency Injection Container.
 *
 * @return Container
 */
function container()
{
    static $container = null;
    if ($container === null) {
        $container = new Container();
    }
    return $container;
}

/**
 * Get or set config options
 *
 * @return \Odan\Config\ConfigBag
 */
function config()
{
    static $config = null;
    if ($config === null) {
        $config = new \Odan\Config\ConfigBag();
    }
    return $config;
}

/**
 * The request function returns the current request instance.
 *
 * @return Request
 */
function request()
{
    $container = container();
    $request = $container->has('request') ? $container->get('request') : null;
    if (!$request) {
        $request = ServerRequestFactory::fromGlobals();
        $container->share('request', $request);
    }
    return $request;
}

/**
 * The request function returns the current response instance.
 *
 * @return Response
 */
function response()
{
    $container = container();
    $response = $container->has('response') ? $container->get('response') : null;
    if (!$response) {
        $response = new Response();
        $container->share('response', $response);
    }
    return $response;
}

/**
 * The emitter.
 *
 * @return SapiEmitter
 */
function emitter()
{
    $container = container();
    $emitter = $container->has('emitter') ? $container->get('emitter') : null;
    if (!$emitter) {
        $emitter = new SapiEmitter();
        $container->share('emitter', $emitter);
    }
    return $emitter;
}

/**
 * Route collection.
 *
 * @return RouteCollection
 */
function router()
{
    $container = container();
    $route = $container->has('router') ? $container->get('router') : null;
    if (!$route) {
        $route = new RouteCollection($container);
        $container->share('router', $route);
    }
    return $route;
}

/**
 * Text translation (I18n)
 *
 * @param string $message
 * @param ...$context
 * @return string
 *
 * <code>
 * echo __('Hello');
 * echo __('There are %s persons logged', 7);
 * </code>
 */
function __($message)
{
    $translated = translator()->trans($message);
    $context = array_slice(func_get_args(), 1);
    if (!empty($context)) {
        $translated = vsprintf($translated, $context);
    }
    return $translated;
}

/**
 * Translator
 *
 * @return Translator
 */
function translator()
{
    return container()->get(Translator::class);
}

/**
 * Set locale
 *
 * @param string $locale
 * @param string $domain
 * @return void
 */
function set_locale($locale = 'en_US', $domain = 'messages')
{
    $moFile = sprintf('%s/%s_%s.mo', config()->get('locale_path'), $locale, $domain);

    $translator = new Translator($locale, new MessageSelector());
    $translator->addLoader('mo', new MoFileLoader());

    $translator->addResource('mo', $moFile, $locale, $domain);
    $translator->setLocale($locale);

    // Inject translator into function
    container()->share(Translator::class, $translator);
}

/**
 * Get the session object.
 *
 * @return Session
 */
function session()
{
    $container = container();
    $session = $container->has('session') ? $container->get('session') : null;
    if (!$session) {
        $config = config();
        $sessionFactory = new SessionFactory();
        $cookieParams = request()->getCookieParams();
        $session = $sessionFactory->newInstance($cookieParams);
        $session->setName($config->get('session.name'));
        $session->setCacheExpire($config->get('session.cache_expire', 0));
        $container->share('session', $session);
    }
    return $session;
}

/**
 * Database
 *
 * @return Cake\Database\Connection
 */
function db()
{
    $container = container();
    $db = $container->has('db') ? $container->get('db') : null;
    if (!$db) {
        $driver = new Cake\Database\Driver\Mysql(config()->get('db'));
        $db = new Cake\Database\Connection(['driver' => $driver]);
        $container->share('db', $db);
    }
    return $db;
}

/**
 * Logger
 *
 * @return Logger
 */
function logger()
{
    $container = container();
    $logger = $container->has('logger') ? $container->get('logger') : null;
    if (!$logger) {
        $config = config();
        $logger = new Logger('app');

        $level = $config->get('log_level');
        if (!isset($level)) {
            $level = Logger::ERROR;
        }
        $logFile = $config->get('log_path') . '/log.txt';
        $handler = new RotatingFileHandler($logFile, 0, $level, true, 0775);
        $logger->pushHandler($handler);
        $container->share(Logger::class, $logger);
    }
    return $logger;
}

/**
 * Plates template engine.
 *
 * @return League\Plates\Engine
 */
function view()
{
    $container = container();
    $engine = $container->has('view') ? $container->get('view') : null;
    if (!$engine) {
        $config = config();
        $engine = new League\Plates\Engine($config->get('view_path'), null);

        // Add folder shortcut (assets::file.js)
        $engine->addFolder('assets', $config->get('assets_path'));
        $engine->addFolder('view', $config->get('view_path'));

        // Register Asset extension
        $cacheOptions = array(
            // Enable JavaScript and CSS compression
            'minify' => $config->get('assets_minify'),
            // Public assets cache directory
            'public_dir' => $config->get('public_cache_path'),
            // Internal cache adapter
            'cache' => new FilesystemAdapter('assets-cache', 0, $config->get('assset_cache_path')),
        );
        $engine->loadExtension(new \Odan\Asset\PlatesAssetExtension($cacheOptions));
        $container->share('view', $engine);
    }
    return $engine;
}

/**
 * User session.
 *
 * @return App\Service\User\UserSession
 */
function user()
{
    $container = container();
    $user = $container->has('user') ? $container->get('user') : null;
    if (!$user) {
        $secret = config()->get('app.secret');
        $user = new \App\Service\User\UserSession(session(), db(), $secret);
        $container->share('user', $user);
    }
    return $user;
}

/**
 * Http helper
 *
 * @return \App\Util\Http
 */
function http() {
    $container = container();
    $http = $container->has('http') ? $container->get('http') : null;
    if(!$http) {
        $http = new \App\Util\Http(request(), response());
        $container->share('http', $http);
    }
    return $http;
}

/**
 * Generates a normalized URI for the given path.
 *
 * @param string $path A path to use instead of the current one
 * @param boolean $full return absolute or relative url
 * @return string The normalized URI for the path
 */
function baseurl($path = '', $full = false)
{
    return http()->getBaseUrl($path, $full);
}