<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command.
 */
class CreateMigrationCommand extends AbstractCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('create-migration');
        $this->setDescription('Create a new phinx migration');
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
        $io = new SymfonyStyle($input, $output);

        if (!$name = $io->ask('Enter the name of the migration:')) {
            $output->writeln('Aborted');

            return 1;
        }

        system(sprintf('php vendor/robmorgan/phinx/bin/phinx create %s', $name), $errorLevel);

        if ($errorLevel) {
            $output->writeln(sprintf('<error>The command failed</error>'));
        }

        return (int) $errorLevel;
    }
}
