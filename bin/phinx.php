<?php

require_once __DIR__ . '/../vendor/autoload.php';

chdir(__DIR__ . '/../config');

// Start console
require __DIR__ . '/../vendor/odan/phinx-migrations-generator/bin/phinx-migrations';