<?php

namespace App\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Command.
 */
abstract class AbstractCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Set container.
     *
     * @param ContainerInterface $container
     *
     * @return void
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
