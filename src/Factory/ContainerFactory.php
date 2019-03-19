<?php

namespace App\Factory;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use RuntimeException;
use Slim\Container;
use Slim\Router;

/**
 * Factory.
 */
class ContainerFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Create callback.
     *
     * @param string $className Class name
     *
     * @throws ReflectionException
     * @throws RuntimeException
     *
     * @return mixed The object
     */
    public function create(string $className)
    {
        if ($className === Router::class) {
            return $this->container->get('router');
        }

        if ($className === Container::class) {
            return $this->container;
        }

        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor) {
            $args = $this->resolveParameters($constructor->getParameters());
            $instance = $reflectionClass->newInstanceArgs($args);
        } else {
            $instance = $reflectionClass->newInstanceArgs();
        }

        return $instance;
    }

    /**
     * Resolve parameters.
     *
     * @param ReflectionParameter[] $parameters ReflectionParameter
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     *
     * @return array
     */
    private function resolveParameters(array $parameters): array
    {
        if (empty($parameters)) {
            return [];
        }
        $args = [];

        foreach ($parameters as $param) {
            $paramName = $param->getName();
            $class = $param->getClass();
            $paramClassName = $class ? $class->getName() : '';
            $args[] = $this->getArgumentInstance($paramClassName, $paramName);
        }

        return $args;
    }

    /**
     * Get argument instance from container.
     *
     * @param string $className
     * @param string $variableName
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     *
     * @return mixed|null
     */
    protected function getArgumentInstance(string $className, string $variableName)
    {
        if ($this->container->has($className)) {
            return $this->container->get($className);
        }

        if (class_exists($className)) {
            return $this->create($className);
        }

        if ($variableName === 'container') {
            return $this->container;
        }

        if ($this->container->has($variableName)) {
            return $this->container->get($variableName);
        }

        throw new RuntimeException(sprintf('Dependencies of class %s [%s] cannot be resolved', $className, $variableName));
    }
}
