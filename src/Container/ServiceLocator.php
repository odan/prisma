<?php

namespace App\Container;

use Exception;

/**
 * Service locator
 */
class ServiceLocator
{

    protected $services = [];

    public function set($key, $callback)
    {
        $this->services[$key] = $callback;
    }

    public function get($key)
    {
        if (!isset($this->services[$key])) {
            throw new Exception('Service not defined');
        }
        if (is_callable($this->services[$key])) {
            return call_user_func($this->services[$key], $this);
        }
        return $this->services[$key];
    }

    /**
     * Read php file
     *
     * @param string $file Filename
     * @return mixed
     */
    public function requireFile($file)
    {
        return require $file;
    }
}
