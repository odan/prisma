<?php

$app = read(__DIR__ . '/../src/Config/app.php');
$app->db->driver()->connect();

return array(
    'pdo' => $app->db->driver()->connection()
);
