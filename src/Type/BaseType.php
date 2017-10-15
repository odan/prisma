<?php

namespace App\Type;

use Exception;
use ReflectionClass;

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
    public static function exists($typeValue)
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
    public static function getName($typeValue)
    {
        $class = new ReflectionClass(static::class);
        $consts = array_flip($class->getConstants());
        if (!array_key_exists($typeValue, $consts)) {
            throw new Exception(__('Invalid type ID: %s', $typeValue));
        }
        return $consts[$typeValue];
    }
}
