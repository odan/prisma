<?php

// Debug from console
// set XDEBUG_CONFIG="idekey=xdebug"
// php test.php

require_once __DIR__ . '/bootstrap.php';

$phpunit = new \PHPUnit\TextUI\TestRunner();

try {
    $suite = $phpunit->getTest(__DIR__, '', 'Test.php');

    if ($suite === null) {
        throw new RuntimeException('No tests found');
    }

    $testResults = $phpunit->doRun($suite, [], false);
} catch (PHPUnit\Framework\Exception $e) {
    echo $e->getMessage() . "\n";
    echo 'Unit tests failed.';
}
