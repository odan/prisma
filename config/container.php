<?php

// Service container configuration

use App\Service\User\Localization;
use App\Utility\ErrorHandler;
use App\Service\User\AuthenticationService;
use App\Repository\UserRepository;
use App\Utility\AppSettings;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use Illuminate\Database\Connectors\ConnectionFactory;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Odan\Slim\Csrf\CsrfMiddleware;
use Psr\Container\ContainerInterface as Container;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;
use Illuminate\Database\Connection;

$container = app()->getContainer();

// -----------------------------------------------------------------------------
// Settings
// -----------------------------------------------------------------------------

$container['settings'] = function (Container $container) {
    return $container->get(AppSettings::class)->all();
};

$container['environment'] = function () {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $_SERVER['REAL_SCRIPT_NAME'] = $scriptName;
    $_SERVER['SCRIPT_NAME'] = dirname(dirname($scriptName)) . '/' . basename($scriptName);
    return new Slim\Http\Environment($_SERVER);
};

$container[AppSettings::class] = function () {
    $settings = new AppSettings(require __DIR__ . '/config.php');
    return $settings;
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

// -----------------------------------------------------------------------------
// Alias definitions
// -----------------------------------------------------------------------------

$container[Request::class] = function (Container $container) {
    return $container->get('request');
};

$container[Response::class] = function (Container $container) {
    return $container->get('response');
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

    $config = [
        'driver'    => 'mysql',
        'host'      => $settings['db']['host'],
        'database'  => $settings['db']['database'],
        'username'  => $settings['db']['username'],
        'password'  => $settings['db']['password'],
        'charset'   => $settings['db']['charset'],
        'collation' => $settings['db']['collation'],
        'prefix'    => '',
    ];

    $factory = new ConnectionFactory(new \Illuminate\Container\Container());

    return  $factory->make($config);
};

$container[PDO::class] = function (Container $container) {
    return $container->get(Connection::class)->getPdo();
};

$container[Twig::class] = function (Container $container) {
    $settings = $container->get('settings');
    $viewPath = $settings['twig']['path'];

    $twig = new Twig($viewPath, [
        'cache' => $settings['twig']['cache_enabled'] ? $settings['twig']['cache_path'] : false
    ]);

    /* @var Twig_Loader_Filesystem $loader */
    $loader = $twig->getLoader();
    $loader->addPath($settings['public'], 'public');

    $csrfToken = $container->get(CsrfMiddleware::class)->getToken();
    $twig->getEnvironment()->addGlobal('csrf_token', $csrfToken);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');
    $twig->addExtension(new Slim\Views\TwigExtension($container->get('router'), $basePath));
    $twig->addExtension(new \Odan\Twig\TwigAssetsExtension($twig->getEnvironment(), $settings['assets']));
    $twig->addExtension(new \Odan\Twig\TwigTranslationExtension());

    return $twig;
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

$container[Localization::class] = function (Container $container) {
    $translator = $container->get(Translator::class);
    $segment = $container->get(Session::class)->getSegment('localization');
    $localPath = $container->get('settings')['locale']['path'];
    $localization = new Localization($translator, $segment, $localPath);

    return $localization;
};

$container[CsrfMiddleware::class] = function (Container $container) {
    $session = $container->get(Session::class);
    $csrfValue = $session->getCsrfToken()->getValue();
    $sessionId = $session->getId();
    $csrf = new CsrfMiddleware($sessionId);
    $csrf->setToken($csrfValue);

    // optional settings
    $csrf->setSalt('secret');
    $csrf->setTokenName('__token');
    $csrf->protectJqueryAjax(true);
    $csrf->protectForms(true);

    return $csrf;
};

$container[Translator::class] = function (Container $container) {
    $settings = $container->get('settings')['locale'];
    $translator = new Translator($settings['locale'], new MessageSelector(), $settings['cache']);
    $translator->addLoader('mo', new MoFileLoader());

    return $translator;
};

$container[AuthenticationService::class] = function (Container $container) {
    return new AuthenticationService(
        $container->get(Session::class),
        $container->get(UserRepository::class),
        $container->get('settings')['app']['secret']
    );
};

// -----------------------------------------------------------------------------
// Repositories
// -----------------------------------------------------------------------------
$container[UserRepository::class] = function (Container $container) {
    return new UserRepository($container->get(Connection::class));
};

return $container;
