<?php

// Container configuration

use App\Utility\ErrorHandler;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use DI\Container;
use League\Plates\Engine;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Odan\Asset\PlatesAssetExtension;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

$container = [];

// -----------------------------------------------------------------------------
// Settings
// -----------------------------------------------------------------------------
$container['settings'] = function () {
    return read(__DIR__ . '/config.php');
};

// -----------------------------------------------------------------------------
// Slim definition aliases
// -----------------------------------------------------------------------------
$container[Request::class] = DI\get('request');
$container[Response::class] = DI\get('response');
$container[Router::class] = DI\get('router');
$container['phpErrorHandler'] = DI\get('errorHandler');

// -----------------------------------------------------------------------------
// Slim definitions
// -----------------------------------------------------------------------------

// Handle PHP Exceptions
$container['errorHandler'] = function (Container $container) {
    $displayErrorDetails = $container->get('settings')['displayErrorDetails'];
    $logger = $container->get(LoggerInterface::class);
    return new ErrorHandler((bool)$displayErrorDetails, $logger);
};

// -----------------------------------------------------------------------------
// Custom definitions
// -----------------------------------------------------------------------------
$container[LoggerInterface::class] = function (Container $container) {
    $settings = $container->get('settings');
    $logger = new Logger($settings['logger']['name']);

    $level = $settings['logger']['level'];
    if (!isset($level)) {
        $level = Logger::ERROR;
    }
    $logFile = $settings['logger']['file'];
    $handler = new RotatingFileHandler($logFile, 0, $level, true, 0775);
    $logger->pushHandler($handler);

    return $logger;
};

$container[Engine::class] = function (Container $container) {
    $settings = $container->get('settings');
    $engine = new Engine($settings['view']['path'], null);

    // Add folder shortcut (assets::file.js)
    $engine->addFolder('assets', $settings['assets']['path']);
    $engine->addFolder('view', $settings['view']['path']);

    // Register Asset extension
    $engine->loadExtension(new PlatesAssetExtension((array)$settings['assets']));

    return $engine;
};

$container[Connection::class] = function (Container $container) {
    $settings = $container->get('settings');
    $driver = new Mysql($settings['db']);

    return new Connection(['driver' => $driver]);
};

$container[PDO::class] = function (Container $container) {
    $db = $container->get(Connection::class);
    $db->getDriver()->connect();

    return $db->getDriver()->connection();
};

$container[Session::class] = function (Container $container) {
    $settings = $container->get('settings');
    $sessionFactory = new SessionFactory();
    $cookieParams = $container->get(Request::class)->getCookieParams();
    $session = $sessionFactory->newInstance($cookieParams);
    $session->setName($settings['session']['name']);
    $session->setCacheExpire($settings['session']['cache_expire']);

    return $session;
};

$container[Translator::class] = function (Container $container) {
    $settings = $container->get('settings')['locale'];
    $moFile = sprintf('%s/%s_%s.mo', $settings['path'], $settings['locale'], $settings['domain']);

    $translator = new Translator($settings['locale'], new MessageSelector());
    $translator->addLoader('mo', new MoFileLoader());

    $translator->addResource('mo', $moFile, $settings['locale'], $settings['domain']);
    $translator->setLocale($settings['locale']);

    return $translator;
};

return $container;
