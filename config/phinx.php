<?php

require_once __DIR__ . '/bootstrap.php';

/* @var $pdo PDO */
$pdo = app()->getContainer()->get('PDO');

return array(
    'paths' => [
        'migrations' => app()->getContainer()->get('settings')['migration']['path']
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
