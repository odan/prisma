<?php

// Service container configuration

use App\Controller\Options\ControllerOptions;
use App\Middleware\ErrorHandlerMiddleware;
use App\Service\User\Authentication;
use App\Service\User\AuthenticationOptions;
use App\Service\User\UserRepository;
use App\Utility\AppSettings;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Odan\Database\Connection;
use Odan\Slim\Csrf\CsrfMiddleware;
use Psr\Container\ContainerInterface as Container;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

$container = app()->getContainer();

// -----------------------------------------------------------------------------
// Settings
// -----------------------------------------------------------------------------

$container['callableResolver'] = new Odan\SlimDi\DependencyResolver($container);

$container['settings'] = function (Container $container) {
    return $container->get(AppSettings::class)->all();
};

$container['environment'] = function () {
    // Fix the Slim 3 subdirectory issue (#1529)
    // This fix makes it possible to run the app from localhost/slim_app_dir
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $_SERVER['REAL_SCRIPT_NAME'] = $scriptName;
    $_SERVER['SCRIPT_NAME'] = dirname(dirname($scriptName)) . '/' . basename($scriptName);
    return new Slim\Http\Environment($_SERVER);
};

$container[AppSettings::class] = function () {
    $settings = new AppSettings(require __DIR__ . '/config.php');
    return $settings;
};

$container[Request::class] = function (Container $container) {
    return $container->get('request');
};


$container[Response::class] = function (Container $container) {
    return $container->get('response');
};

// -----------------------------------------------------------------------------
// Custom aliases
// -----------------------------------------------------------------------------
$container[PDO::class] = function ($container) {
    return $container->get(Connection::class);
};

// -----------------------------------------------------------------------------
// Slim definitions
// -----------------------------------------------------------------------------

// Handle PHP Exceptions
$container['errorHandler'] = function (Container $container) {
    $displayErrorDetails = $container->get('settings')['displayErrorDetails'];
    $logger = $container->get(LoggerInterface::class);
    return new ErrorHandlerMiddleware((bool)$displayErrorDetails, $logger);
};

$container['phpErrorHandler'] = function (Container $container) {
    return $container->get('errorHandler');
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

$container[ControllerOptions::class] = function (Container $container) {
    $options = new ControllerOptions();
    $options->router = $container->get('router');
    $options->logger = $container->get(LoggerInterface::class);
    $options->db = $container->get(Connection::class);
    $options->view = $container->get(Twig::class);
    $options->user = $container->get(Authentication::class);

    return $options;
};

$container[Authentication::class] = function (Container $container) {
    return new Authentication(
        $container->get(Session::class),
        $container->get(UserRepository::class),
        $container->get(Translator::class),
        $container->get(AuthenticationOptions::class)
    );
};

$container[AuthenticationOptions::class] = function (Container $container) {
    $settings = $container->get('settings');

    $authSettings = new AuthenticationOptions();
    $authSettings->localePath = $settings['locale']['path'];
    $authSettings->secret = $settings['app']['secret'];

    return $authSettings;
};

$container[UserRepository::class] = function (Container $container) {
    return new UserRepository($container->get(Connection::class));
};

return $container;
