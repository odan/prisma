<?php

namespace App\Task;

use Psr\Container\ContainerInterface;
use RuntimeException;

class ExampleTask
{

    /** @var ContainerInterface */
    private $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * SampleTask command
     *
     * @param array $args
     * @return string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function command($args)
    {
        // Access items in container
        $settings = $this->container->get('settings');

        // Throw if no arguments provided
        if (empty($args)) {
            throw new RuntimeException("No arguments passed to command");
        }

        $firstArg = $args[0];

        // Output the first argument
        return $firstArg;
    }
}
