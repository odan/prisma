<?php

// Service container configuration

use App\Utility\ErrorHandler;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use DI\Container;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
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

$container['environment'] = function () {
    // Fix the Slim 3 subdirectory issue (#1529)
    // This fix makes it possible to run the app from localhost/slim_app_dir
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $_SERVER['REAL_SCRIPT_NAME'] = $scriptName;
    $_SERVER['SCRIPT_NAME'] = dirname(dirname($scriptName)) . '/' . basename($scriptName);
    return new Slim\Http\Environment($_SERVER);
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

$container[\Slim\Views\Twig::class] = function (Container $container) {
    $settings = $container->get('settings');
    $viewPath = $settings['twig']['path'];

    $twig = new \Slim\Views\Twig($viewPath, [
        'cache' => $settings['twig']['cache_enabled'] ? $settings['twig']['cache_path']: false
    ]);

    /* @var Twig_Loader_Filesystem $loader */
    $loader = $twig->getLoader();
    $loader->addPath($settings['public'], 'public');

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');
    $twig->addExtension(new Slim\Views\TwigExtension($container->get('router'), $basePath));
    $twig->addExtension(new \Odan\Twig\TwigAssetsExtension($twig->getEnvironment(), $settings['assets']));
    $twig->addExtension(new \Odan\Twig\TwigTranslationExtension());

    return $twig;
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
