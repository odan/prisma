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
            throw new RuntimeException('Must be an object');
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

    /**
     * Convert to json.
     *
     * @param int $options Options
     * @return string A json string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
