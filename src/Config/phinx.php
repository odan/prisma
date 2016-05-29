<?php

return call_user_func(function () {
    $app = App\Container\Application::getInstance();
    $pdo = $app->db->getConnection()->getPdo();

    return array(
        'paths' => [
            'migrations' => $app->config->get('path.migration')
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
