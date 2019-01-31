<?php

$app = require __DIR__ . '/bootstrap.php';

/* @var \Slim\App $app */
$container = $app->getContainer();

/* @var PDO $pdo */
$pdo = $container->get(PDO::class);

$phinx = $container->get('settings')['phinx'];

$phinx['environments']['local'] = [
    // Set database name
    'name' => $pdo->query('select database()')->fetchColumn(),
    'connection' => $pdo,
];

return $phinx;
