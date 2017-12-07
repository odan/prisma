<?php

if (PHP_SAPI !== 'cli') {
    exit (1);
}

require_once __DIR__ . '/../config/bootstrap.php';

$container = app()->getContainer();
$commands = $container->get('settings')['commands'];

$command = $argv[1];
$args = array_slice($argv, 2);

try {
    if (!array_key_exists($command, $commands)) {
        throw new RuntimeException(sprintf('Command %s not found', $command));
    }

    $class = $commands[$command];

    // Bail if class doesn't exist
    if (!class_exists($class)) {
        throw new RuntimeException(sprintf('Class %s does not exist', $class));
    }

    $task_class = new ReflectionClass($class);

    if (!$task_class->hasMethod('command')) {
        throw new RuntimeException(sprintf('Class %s does not have a command() method', $class));
    }

    if ($task_class->getConstructor()) {
        $task_construct_method = new ReflectionMethod($class, '__construct');
        $construct_params = $task_construct_method->getParameters();

        if (count($construct_params) == 0) {
            // Create a new instance without any args
            $task = $task_class->newInstanceArgs();
        } elseif (count($construct_params) == 1) {
            // Create a new instance and pass the container by reference, if needed
            if ($construct_params[0]->isPassedByReference()) {
                $task = $task_class->newInstanceArgs([&$container]);
            } else {
                $task = $task_class->newInstanceArgs([$container]);
            }
        } else {
            throw new RuntimeException(sprintf('Class %s has an unsupported __construct method', $class));
        }
    } else {
        $task = $task_class->newInstanceWithoutConstructor();
    }

    $cliResponse = $task->command($args);
    echo $cliResponse;
    exit (0);
} catch (Exception $e) {
    echo $e->getMessage();
    exit (1);
}
