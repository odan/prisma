<?php

namespace App\Test\TestCase;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Psr\Container\ContainerInterface as Container;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use Slim\App;

/**
 * Trait.
 */
trait SlimAppTestTrait
{
    /**
     * @var App|null
     */
    protected $app;

    /**
     * Boost Slim.
     *
     * @return void
     */
    protected function bootSlim(): void
    {
        $this->app = require __DIR__ . '/../../config/bootstrap.php';
    }

    /**
     * Shutdown Slim.
     *
     * @return void
     */
    protected function shutdownSlim(): void
    {
        $this->app = null;
    }

    /**
     * Get container.
     *
     * @throws \ReflectionException
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        if ($this->app === null) {
            throw new RuntimeException('App must be initialized');
        }

        $container = $this->app->getContainer();

        $this->setContainer($container, SessionInterface::class, call_user_func(function () {
            $session = new PhpSession();
            $session->setOptions([
                'cache_expire' => 60,
                'name' => 'app',
                'use_cookies' => false,
                'cookie_httponly' => false,
            ]);

            return $session;
        }));

        $this->setContainer($container, LoggerInterface::class, call_user_func(function () {
            $logger = new Logger('test');

            return $logger->pushHandler(new NullHandler());
        }));

        return $container;
    }

    /**
     * Set container entry.
     *
     * @param Container $container
     * @param string $key
     * @param mixed $value
     *
     * @throws \ReflectionException
     *
     * @return void
     */
    protected function setContainer(Container $container, string $key, $value): void
    {
        $class = new ReflectionClass(\Pimple\Container::class);

        $property = $class->getProperty('frozen');
        $property->setAccessible(true);

        $values = $property->getValue($container);
        unset($values[$key]);
        $property->setValue($container, $values);

        // The same like '$container[$key] = $value;' but compatible with phpstan
        $method = new ReflectionMethod(\Pimple\Container::class, 'offsetSet');
        $method->invoke($container, $key, $value);
    }
}
