<?php

namespace App\Factory;

use App\Container\ServiceContainer;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use PDO;

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
        $driver = new Mysql($container->config['db']);
        $connection = new Connection([
            'driver' => $driver
        ]);

        /* @var $pdo \PDO */
        $pdo = $connection->driver()->connect();
        return $connection;
    }
}
