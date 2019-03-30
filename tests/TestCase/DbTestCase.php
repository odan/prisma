<?php

namespace App\Test\TestCase;

use App\Factory\ContainerFactory;
use Cake\Database\Connection;
use PDO;
use ReflectionException;
use RuntimeException;
use Phinx\Console\Command\Migrate;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Database tests.
 */
class DbTestCase extends TestCase
{
    use SlimAppTestTrait;

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->bootSlim();

        $this->setUpDatabase();
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->shutdownSlim();
    }

    /**
     * Call this template method before each test method is run.
     *
     * @throws ReflectionException
     *
     * @return void
     */
    protected function setUpDatabase(): void
    {
        $this->getConnection()->connect();

        $this->createTables();
        $this->truncateTables();

        if (!empty($this->fixtures)) {
            $this->insertFixtures($this->fixtures);
        }
    }

    /**
     * Get Connection.
     *
     * @throws ReflectionException
     *
     * @return Connection The test database connection
     */
    public function getConnection(): Connection
    {
        return $this->getContainer()->get(Connection::class);
    }

    /**
     * Get PDO.
     *
     * @throws ReflectionException
     *
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->getConnection()->getDriver()->getConnection();
    }

    /**
     * Create a new instance.
     *
     * @param string $class class name
     *
     * @throws ReflectionException
     *
     * @return mixed object
     */
    protected function createInstance(string $class)
    {
        return $this->getContainer()->get(ContainerFactory::class)->create($class);
    }

    /**
     * Create tables.
     *
     * @return bool Success
     */
    public function createTables(): bool
    {
        if (defined('DB_TEST_TRAIT_INIT')) {
            return true;
        }

        $this->dropTables();
        $this->migrate();

        define('DB_TEST_TRAIT_INIT', 1);

        return true;
    }

    /**
     * Run phinx migrate command.
     *
     * @throws RuntimeException
     *
     * @return bool Success
     */
    protected function migrate(): bool
    {
        $phinxApplication = new Application();
        $phinxApplication->add(new Migrate());

        $phinxMigrateCommand = $phinxApplication->find('migrate');
        $phinxCommandTester = new CommandTester($phinxMigrateCommand);
        $phinxCommandTester->execute([
            'command' => $phinxMigrateCommand->getName(),
            '--configuration' => __DIR__ . '/../../config/phinx.php',
            '--parser' => 'php',
        ]);

        $phinxDisplay = $phinxCommandTester->getDisplay();
        $phinxStatusCode = $phinxCommandTester->getStatusCode();
        if ($phinxStatusCode > 0 || !strpos($phinxDisplay, 'All Done.')) {
            throw new RuntimeException('Running migration failed');
        }

        return true;
    }

    /**
     * Clean-Up Database. Truncate tables.
     *
     * @throws RuntimeException
     *
     * @return void
     */
    protected function dropTables(): void
    {
        $sql = 'SELECT table_name
                FROM information_schema.tables
                WHERE table_schema = database()';

        $db = $this->getPdo();

        $db->exec('SET UNIQUE_CHECKS=0;');
        $db->exec('SET FOREIGN_KEY_CHECKS=0;');

        $statement = $db->query($sql);

        if (!$statement) {
            throw new RuntimeException('Invalid sql statement');
        }

        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $db->exec(sprintf('DROP TABLE `%s`;', $row['table_name']));
        }

        $db->exec('SET UNIQUE_CHECKS=1;');
        $db->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Clean-Up Database. Truncate tables.
     *
     * @throws RuntimeException
     *
     * @return void
     */
    protected function truncateTables(): void
    {
        $sql = 'SELECT table_name
                FROM information_schema.tables
                WHERE table_schema = database()
                AND update_time IS NOT NULL';

        $db = $this->getPdo();

        $db->exec('SET UNIQUE_CHECKS=0;');
        $db->exec('SET FOREIGN_KEY_CHECKS=0;');

        $statement = $db->query($sql);

        if (!$statement) {
            throw new RuntimeException('Invalid sql statement');
        }

        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $db->exec(sprintf('TRUNCATE TABLE `%s`;', $row['table_name']));
        }

        $db->exec('SET UNIQUE_CHECKS=1;');
        $db->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Iterate over all the fixture rows specified and insert them into their respective tables.
     *
     * @param array $fixtures Fixtures
     *
     * @throws ReflectionException
     *
     * @return void
     */
    protected function insertFixtures(array $fixtures): void
    {
        $db = $this->getConnection();
        $pdo = $this->getPdo();
        foreach ($fixtures as $fixture) {
            $object = new $fixture();
            $table = $object->table;
            $pdo->exec(sprintf('TRUNCATE TABLE `%s`;', $table));

            foreach ($object->records as $row) {
                $db->newQuery()->insert(array_keys($row))->into($table)->values($row)->execute();
            }
        }
    }
}
