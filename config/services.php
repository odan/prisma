<?php

/**
 * Services and helper functions
 */
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\ParameterBag;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

/**
 * A simple PHP Dependency Injection Container.
 *
 * @return ParameterBag
 */
function container()
{
    static $container = null;
    if ($container === null) {
        $container = new ParameterBag();
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
    $request = container()->get(Request::class);
    if (!$request) {
        $request = ServerRequestFactory::fromGlobals();
        container()->set(Request::class, $request);
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
    $response = container()->get(Response::class);
    if (!$response) {
        $response = new Response();
        container()->set(Response::class, $response);
    }
    return $response;
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
    container()->set(Translator::class, $translator);
}

/**
 * Get the session object.
 *
 * @return Symfony\Component\HttpFoundation\Session\Session
 */
function session()
{
    $session = container()->get(Session::class);
    if (!$session) {
        if (php_sapi_name() === 'cli') {
            // In cli-mode
            $storage = new MockArraySessionStorage();
            $session = new Session($storage);
        } else {
            // Not in cli-mode
            $config = (array) config()->get('session');
            $storage = new NativeSessionStorage($config, new NativeFileSessionHandler());
            $session = new Session($storage);
            $session->start();
        }
        container()->set(Session::class, $session);
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
    $db = container()->get(Cake\Database\Connection::class);
    if (!$db) {
        $driver = new Cake\Database\Driver\Mysql(config()->get('db'));
        $db = new Cake\Database\Connection(['driver' => $driver]);
        container()->set(Cake\Database\Connection::class, $db);
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
    $logger = container()->get(Logger::class);
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
        container()->set(Logger::class, $logger);
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
    $engine = container()->get(League\Plates\Engine::class);
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
        container()->set(League\Plates\Engine::class, $engine);
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
    $user = container()->get(App\Service\User\UserSession::class);
    if (!$user) {
        $user = new App\Service\User\UserSession(session());
        $user->setSecret(config()->get('app_secret'));
        container()->set(App\Service\User\UserSession::class, $user);
    }
    return $user;
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
    return Odan\Middleware\Util\Http::baseurl(request(), $path, $full);
}

/**
 * Returns current url.
 *
 * @return string URL
 */
function hosturl()
{
    return Odan\Middleware\Util\Http::hosturl(request());
}
