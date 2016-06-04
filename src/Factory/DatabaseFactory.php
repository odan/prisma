<?php

namespace App\Factory;

use App\Container\ServiceContainer;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;

/**
 * DatabaseFactory
 */
class DatabaseFactory
{

    /**
     * Create database connection
     *
     * @return Connection
     */
    public static function create(ServiceContainer $container)
    {
        return new Connection([
            'driver' => new Mysql($container->config['db'])
        ]);
    }
}
