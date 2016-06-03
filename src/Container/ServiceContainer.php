<?php

namespace App\Container;

class ServiceContainer
{

    /**
     * Configuration
     *
     * @var array
     */
    public $config = null;

    public function __construct()
    {
        $this->config = array();
    }

}
