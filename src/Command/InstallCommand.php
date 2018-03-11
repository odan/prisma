<?php

namespace App\Command;

use Exception;
use PDO;
use PDOException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command.
 */
class InstallCommand extends AbstractCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption('environment', 'e', InputOption::VALUE_OPTIONAL, 'The target environment.');

        $this->setName('install');
        $this->setDescription('Install a new application');
    }

    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return int integer 0 on success, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $settings = $this->container->get('settings');
        $root = $settings['root'];
        $configPath = $root . '/config';

        $this->createEnvFile($output, $configPath);
        $this->generateRandomSecret($output, $configPath);

        $env = '';
        if ($input->hasOption('environment')) {
            $env = $input->getOption('environment');
        }

        try {
            return $this->createNewDatabase($io, $output, $configPath, $root, $env);
        } catch (Exception $exception) {
            $output->writeln(sprintf('<error>Unknown error: %s</error> ', $exception->getMessage()));

            return 1;
        }
    }

    protected function createEnvFile(OutputInterface $output, $configPath)
    {
        $output->writeln('Create env.php');
        copy($configPath . '/env.example.php', $configPath . '/env.php');
    }

    protected function generateRandomSecret(OutputInterface $output, $configPath)
    {
        $output->writeln('Generate random app secret');
        file_put_contents($configPath . '/default.php', str_replace('{{app_secret}}', bin2hex(random_bytes(20)), file_get_contents($configPath . '/default.php')));
    }

    protected function createNewDatabase(SymfonyStyle $io, OutputInterface $output, string $configPath, string $root, string $env = null)
    {
        if ($env == 'travis') {
            $mySqlHost = '127.0.0.1';
            $mySqlDatabase = 'test';
            $mySqlUsername = 'root';
            $mySqlPassword = '';
        } else {
            // MySQL setup
            if (!$mySqlHost = $io->ask('Enter MySQL host', '127.0.0.1')) {
                $output->writeln('Aborted');

                return 1;
            }
            if (!$mySqlDatabase = $io->ask('Enter MySQL database name', 'prisma')) {
                $output->writeln('Aborted');

                return 1;
            }

            $mySqlUsername = $io->ask('Enter MySQL username:', 'root');
            $mySqlPassword = $io->ask('Enter MySQL password:', '', function ($string) {
                return $string ? $string : '';
            });
        }

        try {
            $output->writeln('Create database: ' . $mySqlDatabase);

            $pdo = $this->getPdo($mySqlHost, $mySqlUsername, $mySqlPassword);
            $this->createDatabase($pdo, $mySqlDatabase);
            $this->updateDevelopmentSettings($output, $mySqlDatabase, $mySqlUsername, $mySqlPassword, $configPath);
            $this->installDatabaseTables($output, $pdo, $mySqlDatabase, $root);
            $this->seedDatabaseTables($output, $pdo, $mySqlDatabase, $root);

            $output->writeln('<info>Setup successfully<info>');

            return 0;
        } catch (PDOException $ex) {
            $output->writeln(sprintf('<error>Database error: %s</error> ', $ex->getMessage()));

            return 1;
        }
    }

    protected function getPdo($host, $username, $password)
    {
        $pdo = new PDO(
            "mysql:host=$host;charset=utf8",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8 COLLATE utf8_unicode_ci',
            ]
        );

        return $pdo;
    }

    protected function createDatabase(PDO $pdo, string $dbName)
    {
        $dbNameQuoted = $this->quoteName($dbName);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbNameQuoted;");
    }

    protected function quoteName($name)
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }

    protected function updateDevelopmentSettings(OutputInterface $output, $dbName, $username, $password, $configPath)
    {
        $output->writeln('Update development configuration');
        file_put_contents($configPath . '/development.php', str_replace('{{db_database}}', $dbName, file_get_contents($configPath . '/development.php')));
        file_put_contents($configPath . '/env.php', str_replace('{{db_username}}', $username, file_get_contents($configPath . '/env.php')));
        file_put_contents($configPath . '/env.php', str_replace('{{db_password}}', $password, file_get_contents($configPath . '/env.php')));
    }

    protected function installDatabaseTables(OutputInterface $output, PDO $pdo, $dbName, $root)
    {
        $output->writeln('Install database tables');

        $dbNameQuoted = $this->quoteName($dbName);
        $pdo->exec("USE $dbNameQuoted;");

        chdir($root);
        system('php vendor/robmorgan/phinx/bin/phinx migrate');
    }

    protected function seedDatabaseTables(OutputInterface $output, PDO $pdo, $dbName, $root)
    {
        $output->writeln('Seed database tables');

        $dbNameQuoted = $this->quoteName($dbName);
        $pdo->exec("USE $dbNameQuoted;");

        chdir($root);
        system('php vendor/robmorgan/phinx/bin/phinx seed:run');
    }
}
