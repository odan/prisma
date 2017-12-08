<?php

if (PHP_SAPI !== 'cli') {
    exit (1);
}

require_once __DIR__ . '/../config/bootstrap.php';

$container = app()->getContainer();
$commands = $container->get('settings')['commands'];

$application = new \Symfony\Component\Console\Application();

foreach ($commands as $class) {
    if (!class_exists($class)) {
        throw new RuntimeException(sprintf('Class %s does not exist', $class));
    }
    $command = new $class();
    if(method_exists ($command, 'setContainer')) {
        $command->setContainer($container);
    }
    $application->add($command);
}

$application->run();