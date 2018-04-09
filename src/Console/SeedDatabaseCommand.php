<?php

namespace App\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command.
 */
class SeedDatabaseCommand extends AbstractCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('seed-database');
        $this->setDescription('Data seeding');
    }

    /**
     * Execute.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int integer 0 on success, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = __DIR__ . '/../../phinx.php';
        $command = sprintf('php vendor/robmorgan/phinx/bin/phinx seed:run -c %s', $configFile);
        system($command, $errorLevel);

        if ($errorLevel) {
            $output->writeln(sprintf('<error>The command failed</error>'));
        }

        return (int)$errorLevel;
    }
}
