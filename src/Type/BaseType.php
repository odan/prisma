<?php

namespace App\Type;

use Exception;
use ReflectionClass;
use RuntimeException;

/**
 * BaseType
 */
class BaseType
{

    /**
     * Check if code is valid.
     *
     * @param mixed $typeValue Value
     * @return bool True if code exists
     */
    public static function exists($typeValue): bool
    {
        $class = new ReflectionClass(static::class);
        return in_array($typeValue, $class->getConstants());
    }

    /**
     * Get name of constant by value.
     *
     * @param mixed $typeValue Value
     * @return string Name
     * @throws Exception
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
