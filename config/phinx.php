<?php

require_once __DIR__ . '/bootstrap.php';

/* @var $db \Cake\Database\Connection */
$db = container()->get('db');
$db->getDriver()->connect();
$pdo = $db->getDriver()->connection();

return array(
    'paths' => [
        'migrations' => settings()->get('migration')['path']
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
