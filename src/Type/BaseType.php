<?php

namespace App\Type;

use Exception;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

/**
 * BaseType.
 */
abstract class BaseType
{
    /**
     * Check if code is valid.
     *
     * @param mixed $typeValue Value
     *
     * @throws ReflectionException
     *
     * @return bool True if code exists
     */
    public static function exists($typeValue): bool
    {
        $class = new ReflectionClass(static::class);

        return in_array($typeValue, $class->getConstants(), true);
    }

    /**
     * Get name of constant by value.
     *
     * @param mixed $typeValue Value
     *
     * @throws Exception
     *
     * @return string Name
     */
    public static function getName($typeValue): string
    {
        $class = new ReflectionClass(static::class);
        $constants = array_flip($class->getConstants());

        if (!array_key_exists($typeValue, $constants)) {
            throw new RuntimeException(__('Invalid type ID: %s', $typeValue));
        }

        return $constants[$typeValue];
    }
}
