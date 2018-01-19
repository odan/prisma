<?php

$app = require __DIR__ . '/bootstrap.php';

/* @var \Slim\App $app */
$container = $app->getContainer();

/* @var PDO $pdo */
$pdo = $container->get('PDO');

return array(
    'paths' => [
        'migrations' => $container->get('settings')['migration']['path']
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
