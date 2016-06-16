<?php

return call_user_func(function () {

    $app = read(__DIR__ . '/app.php');
    $app->db->driver()->connect();
    $pdo = $app->db->driver()->connection();

    return array(
        'paths' => [
            'migrations' => $app->options['migration']['path']
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
