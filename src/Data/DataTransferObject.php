<?php

namespace App\Data;

use Cake\Utility\Inflector;
use DateTimeImmutable;
use ReflectionException;
use ReflectionParameter;

/**
 * Data Transfer Object (DTO).
 *
 * Only data without complex behavior.
 */
abstract class DataTransferObject
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
    protected function fromArray(array $data): self
    {
        $methods = array_flip(get_class_methods(get_class($this)));
        $class = get_class($this);

        foreach ($data as $name => $value) {
            $method = Inflector::variable('set_' . $name);

            if (!isset($methods[$method])) {
                continue;
            }

            $this->$method($this->castValue($class, $method, $value));
        }

        return $this;
    }

    /**
     * Map value data type.
     *
     * @param string $class The class name
     * @param string $method The method name
     * @param mixed $value The default value
     *
     * @return mixed The value
     */
    protected function castValue(string $class, string $method, $value)
    {
        $parameter = new ReflectionParameter([$class, $method], 0);
        $type = $parameter->getType();

        if (!$type) {
            return $value;
        }

        $dataType = $type->getName();
        if ($dataType === 'DateTimeImmutable') {
            $value = new DateTimeImmutable($value);
        }

        return $value;
    }

    /**
     * Convert to array.
     *
     * @return array Data
     */
    public function toArray(): array
    {
        $array = [];
        $methods = get_class_methods(get_class($this));

        foreach ($methods as $method) {
            preg_match('/^(get|is)(.*?)$/i', $method, $matches);

            if (!isset($matches[2])) {
                continue;
            }

            $key = Inflector::underscore($matches[2]);
            $value = $this->$method();

            // Convert to date time string
            if ($value instanceof DateTimeImmutable) {
                $array[$key] = $value->format('Y-m-d H:i:s');
                continue;
            }

            // Convert booleans into other values (such as 0 and 1)
            if (is_bool($value)) {
                $array[$key] = $value ? 1 : 0;
                continue;
            }

            $array[$key] = $value;
        }

        return $array;
    }
}

