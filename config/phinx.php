<?php

use App\Middleware\CakeDatabaseMiddleware;

return call_user_func(function () {
    // Load config
    $config = read(__DIR__ . '/config.php');

    // Create database object
    $middleware = new CakeDatabaseMiddleware($config['db']);
    $db = $middleware->create();

    // Get PDO object
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
