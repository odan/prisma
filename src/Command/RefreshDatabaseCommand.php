<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command.
 */
class RefreshDatabaseCommand extends AbstractCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('refresh-database');
        $this->setDescription('Reset, migrate and seed database');
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
        system('php cli.php reset-database', $errorLevel);

        if ($errorLevel) {
            $output->writeln(sprintf('<error>The command failed</error>'));
        }

        system('php cli.php migrate-database', $errorLevel);

        if ($errorLevel) {
            $output->writeln(sprintf('<error>The command failed</error>'));
        }

        system('php cli.php seed-database', $errorLevel);

        if ($errorLevel) {
            $output->writeln(sprintf('<error>The command failed</error>'));
        }

        return (int) $errorLevel;
    }
}
