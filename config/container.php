<?php

use DI\Container;
use Monolog\Logger;

// Container configuration
$container = [];

// -----------------------------------------------------------------------------
// Slim factories
// -----------------------------------------------------------------------------

$container['settings'] = function () {
    return read(__DIR__ . '/config.php');
};

// Handle PHP Exceptions
$container['errorHandler'] = function (Container $container) {
    $displayErrorDetails = $container->get('settings')['displayErrorDetails'];
    $logger = $container->get('logger');
    return new App\Utility\ErrorHandler((bool)$displayErrorDetails, $logger);
};

// Handle PHP 7 Errors
$container['phpErrorHandler'] = function (Container $container) {
    return $container->get('errorHandler');
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------
$container['logger'] = function (Container $container) {
    $settings = $container->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);

    $level = $settings['logger']['level'];
    if (!isset($level)) {
        $level = Logger::ERROR;
    }
    $logFile = $settings['logger']['file'];
    $handler = new Monolog\Handler\RotatingFileHandler($logFile, 0, $level, true, 0775);
    $logger->pushHandler($handler);

    return $logger;
};

$container[Psr\Log\LoggerInterface::class] = function (Container $container) {
    $settings = $container->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);

    $level = $settings['logger']['level'];
    if (!isset($level)) {
        $level = Logger::ERROR;
    }
    $logFile = $settings['logger']['file'];
    $handler = new Monolog\Handler\RotatingFileHandler($logFile, 0, $level, true, 0775);
    $logger->pushHandler($handler);

    return $logger;
};


$container[Slim\Http\Request::class] = function (Container $container) {
    $res = $container->get('request');
    return $res;
};

$container[Slim\Http\Response::class] = function (Container $container) {
    return $container->get('response');
};

$container[\Slim\Router::class] = function (Container $container) {
    return  $container->get('router');
};

$container['view'] = function (Container $container) {
    $settings = $container->get('settings');
    $engine = new League\Plates\Engine($settings['view']['path'], null);

    // Add folder shortcut (assets::file.js)
    $engine->addFolder('assets', $settings['assets']['path']);
    $engine->addFolder('view', $settings['view']['path']);

    // Register Asset extension
    $engine->loadExtension(new \Odan\Asset\PlatesAssetExtension((array)$settings['assets']));
    return $engine;
};

$container[\League\Plates\Engine::class] = function (Container $container) {
    $settings = $container->get('settings');
    $engine = new League\Plates\Engine($settings['view']['path'], null);

    // Add folder shortcut (assets::file.js)
    $engine->addFolder('assets', $settings['assets']['path']);
    $engine->addFolder('view', $settings['view']['path']);

    // Register Asset extension
    $engine->loadExtension(new \Odan\Asset\PlatesAssetExtension((array)$settings['assets']));
    return $engine;
};

$container['db'] = function (Container $container) {
    $settings = $container->get('settings');
    $driver = new Cake\Database\Driver\Mysql($settings['db']);
    return new Cake\Database\Connection(['driver' => $driver]);
};

$container[Cake\Database\Connection::class] = function (Container $container) {
    $settings = $container->get('settings');
    $driver = new Cake\Database\Driver\Mysql($settings['db']);
    return new Cake\Database\Connection(['driver' => $driver]);
};
App\Service\User\UserSession::class;

$container['pdo'] = function (Container $container) {
    /* @var \Cake\Database\Connection $db */
    $db = $container->get('db');
    $db->getDriver()->connect();
    return $db->getDriver()->connection();
};

$container['session'] = function (DI\Container $container) {
    $settings = $container->get('settings');
    $sessionFactory = new \Aura\Session\SessionFactory();
    $cookieParams = $container->get('request')->getCookieParams();
    $session = $sessionFactory->newInstance($cookieParams);
    $session->setName($settings['session']['name']);
    $session->setCacheExpire($settings['session']['cache_expire']);
    return $session;
};
$container[Aura\Session\Session::class] = function (DI\Container $container) {
    $settings = $container->get('settings');
    $sessionFactory = new \Aura\Session\SessionFactory();
    $cookieParams = $container->get('request')->getCookieParams();
    $session = $sessionFactory->newInstance($cookieParams);
    $session->setName($settings['session']['name']);
    $session->setCacheExpire($settings['session']['cache_expire']);
    return $session;
};


$container['user'] = function (Container $container) {
    $settings = $container->get('settings');
    $session = $container->get('session');
    $db = $container->get('db');
    $secret = $settings['app']['secret'];
    $user = new \App\Service\User\UserSession($session, $db, $secret);
    return $user;
};

return $container;
