<?php

namespace App\Entity;

use Illuminate\Support\Str;
use RuntimeException;
use stdClass;

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
            $this->fromObject((object)$values, $this);
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
     * @param stdClass|mixed $source
     * @param stdClass|mixed $destination
     * @return stdClass|mixed $destination
     */
    private function fromObject($source, $destination)
    {
        $properties = array_fill_keys(array_keys((array)get_object_vars($destination)), 1);

        foreach ($source as $name => $value) {
            $property = Str::camel($name);
            if (isset($properties[$property])) {
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
        $array = [];
        $properties = get_object_vars($this);

        foreach ($properties as $property => $value) {
            $array[Str::snake($property)] = $this->{$property};
        }

        return $array;
    }
}
