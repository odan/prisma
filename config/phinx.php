<?php

require_once __DIR__ . '/bootstrap.php';

// Get PDO object
$db = db();
$db->getDriver()->connect();
$pdo = $db->getDriver()->connection();

return array(
    'paths' => [
        'migrations' => config()->get('migration_path')
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
