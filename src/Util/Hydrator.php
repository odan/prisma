<?php

namespace App\Util;

class Hydrator
{
    const CAMEL_CASE = 1;
    const SNAKE_CASE = 2;

    /**
     * @var StringUtil
     */
    protected $stringUtil;

    public function __construct()
    {
        $this->stringUtil = new StringUtil();
    }

    /**
     * Convert array to value object.
     *
     * @param array $array
     * @param string $type
     * @return mixed Value object
     */
    public function toObject($array, $type)
    {
        if (empty($array)) {
            return null;
        }
        $object = new $type();
        $methods = array_flip(get_class_methods(get_class($object)));
        foreach ($array as $key => $value) {
            $method = $this->stringUtil->camel('set_' . $key);
            if (isset($methods[$method])) {
                $object->$method($value);
            }
        }
        return $object;
    }

    /**
     * Convert array to value object.
     *
     * @param array $rows
     * @param string $type
     * @return array Collection of value objects
     */
    public function toCollection($rows, $type)
    {
        if (empty($rows)) {
            return null;
        }
        $collection = [];
        $methods = array_flip(get_class_methods(get_class(new $type())));
        foreach ($rows as $row) {
            $object = new $type();
            foreach ($row as $key => $value) {
                $method = $this->stringUtil->camel('set_' . $key);
                if (!isset($methods[$method])) {
                    continue;
                }
                $object->$method($value);
            }
            $collection[] = $object;
        }
        return $collection;
    }

    /**
     * Convert value object to array.
     *
     * @param mixed $object
     * @param int $keyCase Key case
     * @return array
     */
    public function toArray($object, $keyCase = 1)
    {
        $array = array();
        $methods = get_class_methods(get_class($object));

        foreach ($methods as $method) {
            preg_match(' /^(get)(.*?)$/i', $method, $matches);
            if (!isset($matches[2])) {
                continue;
            }
            if ($keyCase === static::CAMEL_CASE) {
                $key = $this->stringUtil->camel($matches[2]);
            } else {
                $key = $this->stringUtil->snake($matches[2]);
            }
            $array[$key] = $object->$method();
        }
        return $array;
    }
}
