<?php

namespace App\Utility;

use ReflectionClass;
use ReflectionParameter;
use RuntimeException;
use Slim\CallableResolver;
use Slim\Container;
use Slim\Interfaces\CallableResolverInterface;

/**
 * Class DependencyResolver
 */
class DependencyResolver implements CallableResolverInterface
{
    /**
     * Slim callable pattern
     */
    const CALLABLE_PATTERN = '!^([^\:]+)\:([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$!';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var CallableResolver
     */
    private $resolver;

    /**
     * Constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->resolver = new CallableResolver($container);
    }

    /**
     * Invoke the resolved callable.
     *
     * @param mixed $toResolve
     * @return callable
     */
    public function resolve($toResolve)
    {
        if (is_callable($toResolve)) {
            return $toResolve;
        }
        if (!is_string($toResolve)) {
            $this->assertCallable($toResolve);
        }
        list($className, $method) = $this->getSlimCallable($toResolve);
        if ($this->container->has($className)) {
            return $this->resolver->resolve($toResolve);
        }
        return $this->createCallback($className, $method);
    }

    /**
     * Check for slim callable as "class:method".
     *
     * @param mixed $toResolve ID
     * @return array class name and method name
     * @throws RuntimeException Invalid callable
     */
    protected function getSlimCallable($toResolve)
    {
        if (preg_match(self::CALLABLE_PATTERN, $toResolve, $matches)) {
            $className = $matches[1];
            $method = $matches[2];
            return [$className, $method];
        }
        throw new RuntimeException(sprintf('Invalid callable: %s', $toResolve));
    }

    /**
     * Create callback.
     *
     * @param string $className Class name
     * @param string $method Method name
     * @return array Callable
     * @throws RuntimeException Class not found
     */
    protected function createCallback($className, $method)
    {
        if (!class_exists($className)) {
            throw new RuntimeException(sprintf('Class %s does not exist', $className));
        }
        $reflectionClass = new ReflectionClass($className);
        $args = $this->resolveParameters($reflectionClass->getConstructor()->getParameters(), $className);
        $instance = $reflectionClass->newInstanceArgs($args);
        return [$instance, $method];
    }

    /**
     * Resolve parameters.
     *
     * @param ReflectionParameter[] $parameters ReflectionParameter
     * @param string $className Class name
     * @return array
     * @throws RuntimeException
     */
    protected function resolveParameters($parameters, $className)
    {
        if (empty($parameters)) {
            return [];
        }

        $args = [];
        foreach ($parameters as $param) {
            $arg = null;
            $paramClassName = $param->getClass()->getName();
            $name = $param->getName();
            if ($this->container->offsetExists($paramClassName)) {
                $arg = $this->container->get($paramClassName);
            }
            if (!$arg && $this->container->offsetExists($name)) {
                $arg = $this->container->get($name);
            }
            if (!$arg) {
                throw new RuntimeException(sprintf('Dependencies of class %s cannot be resolved', $className));
            }
            $args[] = $arg;
        }
        return $args;
    }

    /**
     * Assert callable.
     *
     * @param mixed $callable Callable
     * @throws RuntimeException if the callable is not resolvable
     * @return bool Status
     */
    protected function assertCallable($callable)
    {
        if (!is_callable($callable)) {
            throw new RuntimeException(sprintf(
                '%s is not resolvable',
                is_array($callable) || is_object($callable) ? json_encode($callable) : $callable
            ));
        }
        return true;
    }
}
