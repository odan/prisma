<?php

namespace App\Command;

use PDO;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command.
 */
class ResetDatabaseCommand extends AbstractCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('reset-database');
        $this->setDescription('Drop all database tables');
    }

    /**
     * Clear database, drop all tables.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return int integer 0 on success, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var PDO $pdo */
        $pdo = $this->container->get(PDO::class);

        // Drop all tables for the rollback command
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

        $rows = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        foreach ($rows as $table) {
            $output->writeln(sprintf('<info>Drop table:</info> %s', $table));
            $pdo->exec(sprintf('DROP TABLE `%s`;', $table));
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');

        return 0;
    }
}
