<?php

// Service container configuration

use App\Service\User\Auth;
use App\Service\User\AuthRepository;
use App\Service\User\Locale;
use App\Repository\UserRepository;
use App\Utility\ErrorHandler;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Odan\Slim\Csrf\CsrfMiddleware;
use Odan\Slim\Session\Adapter\MemorySessionAdapter;
use Odan\Slim\Session\Adapter\PhpSessionAdapter;
use Odan\Slim\Session\Session;
use Psr\Container\ContainerInterface as Container;
use Psr\Log\LoggerInterface;
use Slim\Handlers\NotFound;
use Slim\Views\Twig;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

/** @var \Slim\App $app */
$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Settings
// -----------------------------------------------------------------------------

$container['environment'] = function () {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $_SERVER['REAL_SCRIPT_NAME'] = $scriptName;
    $_SERVER['SCRIPT_NAME'] = dirname(dirname($scriptName)) . '/' . basename($scriptName);

    return new Slim\Http\Environment($_SERVER);
};

// -----------------------------------------------------------------------------
// Slim definitions
// -----------------------------------------------------------------------------

// Handle PHP Exceptions
$container['errorHandler'] = function (Container $container) {
    $displayErrorDetails = $container->get('settings')['displayErrorDetails'];
    $logger = $container->get(LoggerInterface::class);

    return new ErrorHandler((bool)$displayErrorDetails, $logger);
};

$container['phpErrorHandler'] = function (Container $container) {
    return $container->get('errorHandler');
};

$container['notFoundHandler'] = function (Container $container) {
    $logger = $container->get(LoggerInterface::class);
    $logger->error('Error 404: Not found.', ['server' => $_SERVER]);

    return new NotFound();
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

$container[Connection::class] = function (Container $container) {
    $settings = $container->get('settings');
    $driver = new Mysql($settings['db']);

    return new Connection(['driver' => $driver]);
};

$container[PDO::class] = function (Container $container) {
    /** @var Connection $db */
    $db = $container->get(Connection::class);
    $db->getDriver()->connect();

    return $db->getDriver()->getConnection();
};

$container[Twig::class] = function (Container $container) {
    $settings = $container->get('settings');
    $viewPath = $settings['twig']['path'];

    $twig = new Twig($viewPath, [
        'cache' => $settings['twig']['cache_enabled'] ? $settings['twig']['cache_path'] : false,
    ]);

    /** @var Twig_Loader_Filesystem $loader */
    $loader = $twig->getLoader();
    if ($loader instanceof Twig_Loader_Filesystem) {
        $loader->addPath($settings['public'], 'public');
    }

    // Add CSRF token as global template variable
    $csrfToken = $container->get(CsrfMiddleware::class)->getToken();
    $twig->getEnvironment()->addGlobal('csrf_token', $csrfToken);

    // Add Slim specific extensions
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment($container->get('environment'));
    $twig->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    $twig->addExtension(new \Odan\Twig\TwigAssetsExtension($twig->getEnvironment(), (array)$settings['assets']));
    $twig->addExtension(new \Odan\Twig\TwigTranslationExtension());

    return $twig;
};

$container[Session::class] = function (Container $container) {
    $settings = $container->get('settings');
    $adapter = php_sapi_name() === 'cli' ? new MemorySessionAdapter() : new PhpSessionAdapter();
    $session = new Session($adapter);
    $session->setOptions((array)$settings['session']);

    return $session;
};

$container[Locale::class] = function (Container $container) {
    $translator = $container->get(Translator::class);
    $session = $container->get(Session::class);
    $localPath = $container->get('settings')['locale']['path'];
    $localization = new Locale($translator, $session, $localPath);

    return $localization;
};

$container[CsrfMiddleware::class] = function (Container $container) {
    if (php_sapi_name() === 'cli') {
        $sessionId = 'cli';
    } else {
        $session = $container->get(Session::class);
        $sessionId = $session->getId();
    }
    $csrf = new CsrfMiddleware($sessionId);

    // optional settings
    $csrf->setSalt('secret');
    $csrf->setTokenName('__token');
    $csrf->protectJqueryAjax(true);
    $csrf->protectForms(true);

    return $csrf;
};

$container[Translator::class] = function (Container $container) {
    $settings = $container->get('settings')['locale'];
    $translator = new Translator($settings['locale'], new MessageFormatter(new MessageSelector()), $settings['cache']);
    $translator->addLoader('mo', new MoFileLoader());

    return $translator;
};

$container[Auth::class] = function (Container $container) {
    return new Auth($container->get(Session::class), $container->get(AuthRepository::class));
};

// -----------------------------------------------------------------------------
// Repositories
// -----------------------------------------------------------------------------
$container[UserRepository::class] = function (Container $container) {
    return new UserRepository($container->get(Connection::class), $container->get(Auth::class));
};

$container[AuthRepository::class] = function (Container $container) {
    return new AuthRepository($container->get(Connection::class));
};

// -----------------------------------------------------------------------------
// Repositories
// -----------------------------------------------------------------------------
$container[\App\Service\User\UserService::class] = function (Container $container) {
    return new \App\Service\User\UserService($container->get(UserRepository::class));
};

return $container;
