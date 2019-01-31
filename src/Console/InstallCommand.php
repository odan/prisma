<?php

namespace App\Console;

use Exception;
use PDO;
use PDOException;
use RuntimeException;
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

            if (!is_string($env) && $env !== null) {
                throw new RuntimeException('Invalid environment');
            }
        }

        try {
            return $this->createNewDatabase($io, $output, $configPath, $root, $env);
        } catch (Exception $exception) {
            $output->writeln(sprintf('<error>Unknown error: %s</error> ', $exception->getMessage()));

            return 1;
        }
    }

    /**
     * Create env.php file.
     *
     * @param OutputInterface $output
     * @param string $configPath
     *
     * @return void
     */
    protected function createEnvFile(OutputInterface $output, string $configPath): void
    {
        $output->writeln('Create env.php');
        copy($configPath . '/env.example.php', $configPath . '/env.php');
    }

    /**
     * Generate a random secret.
     *
     * @param OutputInterface $output
     * @param string $configPath
     *
     * @throws Exception
     *
     * @return void
     */
    protected function generateRandomSecret(OutputInterface $output, string $configPath): void
    {
        $output->writeln('Generate random app secret');
        file_put_contents($configPath . '/defaults.php', str_replace('{{app_secret}}', bin2hex(random_bytes(20)), file_get_contents($configPath . '/defaults.php') ?: ''));
    }

    /**
     * Create a new database.
     *
     * @param SymfonyStyle $io
     * @param OutputInterface $output
     * @param string $configPath
     * @param string $root
     * @param string|null $env
     *
     * @return int
     */
    protected function createNewDatabase(SymfonyStyle $io, OutputInterface $output, string $configPath, string $root, string $env = null): int
    {
        if ($env === 'travis') {
            $mySqlHost = '127.0.0.1';
            $mySqlDatabase = 'test';
            $mySqlUsername = 'root';
            $mySqlPassword = '';
        } else {
            // MySQL setup
            if (!$mySqlHost = $io->ask('Enter MySQL host', 'localhost')) {
                $output->writeln('Aborted');

                return 1;
            }
            if (!$mySqlDatabase = $io->ask('Enter MySQL database name', 'prisma')) {
                $output->writeln('Aborted');

                return 1;
            }

            $mySqlUsername = $io->ask('Enter MySQL username:', 'root');
            $mySqlPassword = $io->ask('Enter MySQL password:', '', function ($string) {
                return $string ?: '';
            });
        }

        try {
            $output->writeln('Create database: ' . $mySqlDatabase);

            $pdo = $this->createPdo($mySqlHost, $mySqlUsername, $mySqlPassword);
            $this->createDatabase($pdo, $mySqlDatabase);
            $this->updateDevelopmentSettings($output, $mySqlHost, $mySqlDatabase, $mySqlUsername, $mySqlPassword, $configPath);
            $this->installDatabaseTables($output, $root);
            $this->seedDatabaseTables($output, $root);

            $output->writeln('<info>Setup successfully<info>');

            return 0;
        } catch (PDOException $ex) {
            $output->writeln(sprintf('<error>Database error: %s</error> ', $ex->getMessage()));

            return 1;
        }
    }

    /**
     * Create a PDO object.
     *
     * @param string $host
     * @param string $username
     * @param string $password
     *
     * @return PDO
     */
    protected function createPdo(string $host, string $username, string $password): PDO
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

    /**
     * Create database.
     *
     * @param PDO $pdo
     * @param string $dbName
     *
     * @return void
     */
    protected function createDatabase(PDO $pdo, string $dbName): void
    {
        $dbNameQuoted = $this->quoteName($dbName);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbNameQuoted;");
    }

    /**
     * Quote name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function quoteName(string $name): string
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }

    /**
     * Update dev settings.
     *
     * @param OutputInterface $output
     * @param string $dbHost
     * @param string $dbName
     * @param string $username
     * @param string $password
     * @param string $configPath
     *
     * @return void
     */
    protected function updateDevelopmentSettings(OutputInterface $output, string $dbHost, string $dbName, string $username, string $password, string $configPath): void
    {
        $output->writeln('Update development configuration');
        file_put_contents($configPath . '/development.php', str_replace('{{db_host}}', $dbHost, file_get_contents($configPath . '/development.php') ?: ''));
        file_put_contents($configPath . '/development.php', str_replace('{{db_database}}', $dbName, file_get_contents($configPath . '/development.php') ?: ''));
        file_put_contents($configPath . '/env.php', str_replace('{{db_username}}', $username, file_get_contents($configPath . '/env.php') ?: ''));
        file_put_contents($configPath . '/env.php', str_replace('{{db_password}}', $password, file_get_contents($configPath . '/env.php') ?: ''));
    }

    /**
     * Install database.
     *
     * @param OutputInterface $output
     * @param string $root
     *
     * @return void
     */
    protected function installDatabaseTables(OutputInterface $output, string $root): void
    {
        $output->writeln('Install database tables');

        chdir($root);
        system('ant migrate-database');
    }

    /**
     * Seed database tables.
     *
     * @param OutputInterface $output
     * @param string $root
     *
     * @return void
     */
    protected function seedDatabaseTables(OutputInterface $output, string $root): void
    {
        $output->writeln('Seed database tables');

        chdir($root);
        system('ant seed-database');
    }
}
