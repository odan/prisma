<?php

// Service container configuration

use App\Utility\ErrorHandler;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use DI\Container;
use League\Plates\Engine;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Odan\Asset\PlatesAssetExtension;
use Odan\Config\ConfigBag;
use Odan\Database\Connection;
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
$container['settings'] = function (Container $container) {
    return $container->get(ConfigBag::class)->export();
};

$container[ConfigBag::class] = function () {
    $config = new ConfigBag();
    $config->load(__DIR__ . '/config.php');
    return $config;
};

// -----------------------------------------------------------------------------
// Slim definition aliases
// -----------------------------------------------------------------------------
$container[Request::class] = DI\get('request');
$container[Response::class] = DI\get('response');
$container[Router::class] = DI\get('router');
$container['phpErrorHandler'] = DI\get('errorHandler');

// -----------------------------------------------------------------------------
// Custom aliases
// -----------------------------------------------------------------------------
$container[PDO::class] = DI\get(Connection::class);

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
    $driver = $settings['db']['driver'];
    $host = $settings['db']['host'];
    $database = $settings['db']['database'];
    $username = $settings['db']['username'];
    $password = $settings['db']['password'];
    $charset = $settings['db']['charset'];
    $collate = $settings['db']['collation'];
    $dsn = "$driver:host=$host;dbname=$database;charset=$charset";
    $options = $settings['db']['flags'];
    $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES $charset COLLATE $collate";

    return new Connection($dsn, $username, $password, $options);
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

    $translator = new Translator($settings['locale'], new MessageSelector(), $settings['cache']);
    $translator->addLoader('mo', new MoFileLoader());

    $translator->addResource('mo', $moFile, $settings['locale'], $settings['domain']);
    $translator->setLocale($settings['locale']);

    return $translator;
};

return $container;
