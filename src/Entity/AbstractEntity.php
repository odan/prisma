<?php

namespace App\Entity;

use Illuminate\Support\Str;
use RuntimeException;

/**
 * Base Entity
 */
abstract class AbstractEntity implements EntityInterface
{

    /**
     * Constructor.
     *
     * BaseEntity constructor.
     * @param mixed $values
     */
    public function __construct($values = null)
    {
        if ($values) {
            $this->hydrate((object)$values, $this);
        }
    }

    /**
     * Magic method.
     *
     * @param string $name
     * @param mixed $value
     * @throws RuntimeException
     */
    public function __set($name, $value)
    {
        throw new RuntimeException(sprintf("Property [%s] doesn't exist for class [%s]. Cannot set value [%s].", $name, get_class($this), $value));
    }

    /**
     * Magic method.
     *
     * @param string $name
     * @throws RuntimeException
     */
    public function __get($name)
    {
        throw new RuntimeException(sprintf("Property [%s] doesn't exist for class [%s].", $name, get_class($this)));
    }

    /**
     * Hydrate array to object.
     *
     * @param mixed $source
     * @param mixed $destination
     * @return mixed $destination
     * @throws RuntimeException
     */
    private function hydrate($source, $destination)
    {
        if (!is_object($destination)) {
            throw new RuntimeException('The destination instance must be of type object');
        }
        $properties = get_class_vars(get_class($destination));
        foreach ($source as $name => $value) {
            $property = Str::camel($name);
            if (array_key_exists($property, $properties)) {
                $destination->{$property} = $value;
            }
        }
        return $destination;
    }

    /**
     * Convert to array.
     *
     * @return array Data
     */
    public function toArray(): array
    {
        $array = array();
        $properties = get_class_vars(get_class($this));
        foreach ($properties as $property => $value) {
            $key = Str::snake($property);
            $array[$key] = $this->{$property};
        }
        return $array;
    }
}
