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

        // check for slim callable as "class:method"
        $method = null;
        if (preg_match(self::CALLABLE_PATTERN, $toResolve, $matches)) {
            $className = $matches[1];
            $method = $matches[2];
            if ($this->container->has($className)) {
                return $this->resolver->resolve($toResolve);
            }
        }

        if (!class_exists($className)) {
            throw new RuntimeException(sprintf('Class %s does not exist', $className));
        }

        $reflectionClass = new ReflectionClass($className);
        if (!$reflectionClass->isInstantiable()) {
            return $this->resolver->resolve($toResolve);
        }

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
     * @param Callable $callable
     * @throws RuntimeException if the callable is not resolvable
     */
    protected function assertCallable($callable)
    {
        if (!is_callable($callable)) {
            throw new RuntimeException(sprintf(
                '%s is not resolvable',
                is_array($callable) || is_object($callable) ? json_encode($callable) : $callable
            ));
        }
    }
}
