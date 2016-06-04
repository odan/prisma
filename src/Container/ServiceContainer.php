<?php

namespace App\Container;

use Cake\Database\Connection;

class ServiceContainer
{

    /**
     * Configuration
     *
     * @var array
     */
    public $config = null;

    /**
     * Database connection
     *
     * @var Connection
     */
    public $db = null;

    public function __construct()
    {
        $this->config = array();
    }

}
