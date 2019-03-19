<?php

// Service container configuration

use App\Domain\User\Auth;
use App\Domain\User\AuthRepository;
use App\Domain\User\Locale;
use App\Factory\ContainerFactory;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\CsrfAjaxMiddleware;
use App\Middleware\LanguageMiddleware;
use App\Middleware\SessionMiddleware;
use App\Middleware\ErrorHandler;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Odan\Session\MemorySession;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Psr\Container\ContainerInterface as Container;
use Psr\Log\LoggerInterface;
use Slim\Csrf\Guard;
use Slim\Handlers\NotFound;
use Slim\Views\Twig;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\Translator;
use Twig\Loader\FilesystemLoader;

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

    return new ErrorHandler($logger, (bool)$displayErrorDetails);
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
    $settings = $container->get('settings')['logger'];
    $level = isset($settings['level']) ?: Logger::ERROR;
    $logFile = $settings['file'];

    $logger = new Logger($settings['name']);
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
    if ($loader instanceof FilesystemLoader) {
        $loader->addPath($settings['public'], 'public');
    }

    // Add CSRF token as global template variable
    $csrf = $container->get(Guard::class);

    $twigEnvironment = $twig->getEnvironment();

    $twigEnvironment->addGlobal('csrf', [
        'keys' => [
            'name' => $csrf->getTokenNameKey(),
            'value' => $csrf->getTokenValueKey(),
        ],
        'name' => $csrf->getTokenName(),
        'value' => $csrf->getTokenValue(),
    ]);

    $twigEnvironment->addGlobal('baseUrl', function () use ($container) {
        return $container->get('router')->pathFor('root');
    });

    $twigEnvironment->addGlobal('globalText', $container->get('globalText'));

    // Add Slim specific extensions
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment($container->get('environment'));
    $twig->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    $twig->addExtension(new \Odan\Twig\TwigAssetsExtension($twig->getEnvironment(), (array)$settings['assets']));
    $twig->addExtension(new \Odan\Twig\TwigTranslationExtension());

    return $twig;
};

$container['globalText'] = function () {
    return [
        'Ok' => __('Ok'),
        'Cancel' => __('Cancel'),
        'Yes' => __('Yes'),
        'No' => __('No'),
    ];
};

$container[SessionInterface::class] = function (Container $container) {
    $settings = $container->get('settings');
    $session = PHP_SAPI === 'cli' ? new MemorySession() : new PhpSession();
    $session->setOptions((array)$settings['session']);

    return $session;
};

$container[Locale::class] = function (Container $container) {
    $translator = $container->get(Translator::class);
    $session = $container->get(SessionInterface::class);
    $localPath = $container->get('settings')['locale']['path'];

    return new Locale($translator, $session, $localPath);
};

$container[Guard::class] = function () {
    $guard = new Guard();

    $guard->setFailureCallable(function ($request, $response, $next) {
        throw new RuntimeException('CSRF middleware failed. Invalid CSRF token. This looks like a cross-site request forgery.');
    });

    return $guard;
};

$container[CsrfAjaxMiddleware::class] = function (Container $container) {
    return new CsrfAjaxMiddleware($container->get(Guard::class));
};

$container[AuthenticationMiddleware::class] = function (Container $container) {
    return new AuthenticationMiddleware($container);
};

$container[SessionMiddleware::class] = function (Container $container) {
    return new SessionMiddleware($container);
};

$container[LanguageMiddleware::class] = function (Container $container) {
    return new LanguageMiddleware($container);
};

$container[CorsMiddleware::class] = function (Container $container) {
    return new CorsMiddleware($container);
};

$container[Translator::class] = function (Container $container) {
    $settings = $container->get('settings')['locale'];
    $translator = new Translator(
        $settings['locale'],
        new MessageFormatter(new IdentityTranslator()),
        $settings['cache']
    );
    $translator->addLoader('mo', new MoFileLoader());

    return $translator;
};

$container[Auth::class] = function (Container $container) {
    return new Auth($container->get(SessionInterface::class), $container->get(AuthRepository::class));
};

$container[AuthRepository::class] = function (Container $container) {
    return new AuthRepository($container->get(Connection::class));
};

$container[ContainerFactory::class] = function (Container $container) {
    return new ContainerFactory($container);
};

$actionFactory = function ($actionClass) {
    return function (Container $container) use ($actionClass) {
        return $container->get(ContainerFactory::class)->create($actionClass);
    };
};

$container[\App\Action\HomeIndexAction::class] = $actionFactory(\App\Action\HomeIndexAction::class);
$container[\App\Action\HomeLoadAction::class] = $actionFactory(\App\Action\HomeLoadAction::class);
$container[\App\Action\HomePingAction::class] = $actionFactory(\App\Action\HomePingAction::class);
$container[\App\Action\UserEditAction::class] = $actionFactory(\App\Action\UserEditAction::class);
$container[\App\Action\UserIndexAction::class] = $actionFactory(\App\Action\UserIndexAction::class);
$container[\App\Action\UserLoginIndexAction::class] = $actionFactory(\App\Action\UserLoginIndexAction::class);
$container[\App\Action\UserLoginSubmitAction::class] = $actionFactory(\App\Action\UserLoginSubmitAction::class);
$container[\App\Action\UserLogoutAction::class] = $actionFactory(\App\Action\UserLogoutAction::class);
$container[\App\Action\UserReviewAction::class] = $actionFactory(\App\Action\UserReviewAction::class);

return $container;
