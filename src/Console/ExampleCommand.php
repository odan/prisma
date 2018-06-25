<?php

namespace App\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command.
 */
class ExampleCommand extends AbstractCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption('environment', 'e', InputOption::VALUE_REQUIRED, 'The target environment.');

        $this->setName('example');
        $this->setDescription('A sample command');
    }

    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int integer 0 on success, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root = $this->container->get('settings')['root'];

        $output->writeln(sprintf('<info>The project root path is:</info> %s', $root));

        return 0;
    }
}
