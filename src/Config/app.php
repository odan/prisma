<?php

$app = new \App\Container\AppContainer();
$app->config = $app->read(__DIR__ . '/config.php');
$app->logger = $app->read(__DIR__ . '/Service/logger.php');
$app->session = $app->read(__DIR__ . '/Service/session.php');
$app->translator = $app->read(__DIR__ . '/Service/translator.php');
$app->db = $app->read(__DIR__ . '/Service/database.php');
$app->view = $app->read(__DIR__ . '/Service/view.php');

return $app;
