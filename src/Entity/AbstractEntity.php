<?php

namespace App\Entity;

use Illuminate\Support\Str;
use RuntimeException;

/**
 * Base DataSet Row.
 */
abstract class AbstractEntity implements EntityInterface
{
    /**
     * Constructor.
     *
     * @param mixed $data Data
     */
    public function __construct($data = null)
    {
        if ($data) {
            $this->fromArray((array)$data);
        }
    }

    /**
     * Hydrate array to object.
     *
     * @param array $data Data
     *
     * @return self
     */
    protected function fromArray(array $data)
    {
        foreach ($data as $name => $value) {
            $property = Str::camel($name);
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }

        return $this;
    }

    /**
     * Magic method.
     *
     * @param string $name
     *
     * @throws RuntimeException
     */
    public function __get($name)
    {
        throw new RuntimeException(sprintf("Property [%s] doesn't exist for class [%s].", $name, get_class($this)));
    }

    /**
     * Magic method.
     *
     * @param string $name
     * @param mixed $value
     *
     * @throws RuntimeException
     */
    public function __set($name, $value)
    {
        throw new RuntimeException(sprintf("Property [%s] doesn't exist for class [%s]. Cannot set value [%s].", $name, get_class($this), $value));
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
