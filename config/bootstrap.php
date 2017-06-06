<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Instantiate a slim application with container
app();

// Load settings
settings()->replace(read(__DIR__ . '/config.php'));

// Set up dependencies
read(__DIR__ . '/factory.php');

// Register middleware
read(__DIR__ . '/middleware.php');

// Register routes
read(__DIR__ . '/routes.php');

// Set default language
set_locale('en_US');