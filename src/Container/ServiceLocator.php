<?php

namespace App\Container;

use Exception;

/**
 * Service locator
 */
class ServiceLocator
{

    /**
     * Services
     *
     * @var array
     */
    protected $services = [];

    /**
     * Set service
     *
     * @param string $key
     * @param mixed|callback $value
     */
    public function set($key, $value)
    {
        $this->services[$key] = $value;
    }

    /**
     * Get service
     *
     * @param string $key
     * @return mixed
     * @throws Exception
     */
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
}
