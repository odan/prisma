<?php

namespace App\Model;

use Cake\Utility\Inflector;
use DateTime;
use DateTimeImmutable;
use ReflectionException;
use ReflectionParameter;

/**
 * Data Transfer Object (DTO) without complex behavior.
 */
abstract class Model implements ModelInterface
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
        $methods = array_flip(get_class_methods(get_class($this)));
        $class = get_class($this);

        foreach ($data as $key => $value) {
            $method = Inflector::variable('set_' . $key);
            if (!isset($methods[$method])) {
                continue;
            }
            $this->$method($this->castValue($class, $method, $value));
        }

        return $this;
    }

    /**
     * Get value.
     *
     * @param string $class The class name
     * @param string $method The method name
     * @param mixed $value The default value
     *
     * @throws ReflectionException
     *
     * @return mixed The value
     */
    protected function castValue(string $class, string $method, $value)
    {
        $parameter = new ReflectionParameter([$class, $method], 0);
        $type = $parameter->getType();

        if ($type) {
            $dataType = $type->getName();
            if ($dataType === 'DateTimeImmutable') {
                $value = new DateTimeImmutable($value);
            }
            if ($dataType === 'DateTime') {
                $value = new DateTime($value);
            }
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
            preg_match('/^(get)(.*?)$/i', $method, $matches);
            if (!isset($matches[2])) {
                continue;
            }
            $key = Inflector::underscore($matches[2]);
            $array[$key] = $this->$method();
        }

        return $array;
    }
}
