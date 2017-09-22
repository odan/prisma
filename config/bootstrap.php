<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Instantiate the slim application
app();

// Register middleware
read(__DIR__ . '/middleware.php');

// Register routes
read(__DIR__ . '/routes.php');
