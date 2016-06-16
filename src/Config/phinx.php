<?php

use App\Middleware\CakeDatabaseMiddleware;

return call_user_func(function () {

    $config = read(__DIR__ . '/config.php');
    $middleware = new CakeDatabaseMiddleware($config['db']);
    $db = $middleware->create();
    $db->driver()->connect();
    $pdo = $db->driver()->connection();

    return array(
        'paths' => [
            'migrations' => $config['migration']['path']
        ],
        'environments' => [
            'default_migration_table' => "phinxlog",
            'default_database' => "local",
            'local' => [
                // Database name
                'name' => $pdo->query('select database()')->fetchColumn(),
                'connection' => $pdo
            ]
        ]
    );
});
