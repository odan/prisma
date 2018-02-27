<?php

$app = require __DIR__ . '/config/bootstrap.php';

/* @var \Slim\App $app */
$container = $app->getContainer();

/* @var PDO $pdo */
$pdo = $container->get('PDO');

$phinx = $container->get('settings')['phinx'];

$phinx['environments']['local'] = [
    // Set database name
    'name' => $pdo->query('select database()')->fetchColumn(),
    'connection' => $pdo,
];

return $phinx;
