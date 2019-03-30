<?php

namespace App\Repository;

use Cake\Database\Connection;
use Cake\Database\Query;
use RuntimeException;

/**
 * Factory.
 */
class QueryFactory
{
    /**
     * Connection.
     *
     * @var Connection
     */
    protected $connection;

    /**
     * @var callable
     */
    protected $beforeUpdateCallback;

    /**
     * @var callable
     */
    protected $beforeInsertCallback;

    /**
     * Constructor.
     *
     * @param Connection $connection the database connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Create a new query.
     *
     * @return Query the query
     */
    public function newQuery(): Query
    {
        return $this->connection->newQuery();
    }

    /**
     * Create a new 'select' query for the given table.
     *
     * @param string $table The table name
     *
     * @return Query A new select query
     */
    public function newSelect(string $table): Query
    {
        $query = $this->newQuery()->from($table);

        if (!$query instanceof Query) {
            throw new RuntimeException('Failed to create query');
        }

        return $query;
    }

    /**
     * Create an 'update' statement for the given table.
     *
     * @param string $table the table to update rows from
     * @param array $data values to be updated [optional]
     *
     * @return Query A new update query
     */
    public function newUpdate(string $table, array $data = []): Query
    {
        if (isset($this->beforeUpdateCallback)) {
            $data = (array)call_user_func($this->beforeUpdateCallback, $data, $table);
        }

        return $this->newQuery()->update($table)->set($data);
    }

    /**
     * Create an 'update' statement for the given table.
     *
     * @param string $table the table to update rows from
     * @param array $data values to be updated
     *
     * @return Query A new insert query
     */
    public function newInsert(string $table, array $data): Query
    {
        if (isset($this->beforeInsertCallback)) {
            $data = (array)call_user_func($this->beforeInsertCallback, $data, $table);
        }

        $columns = array_keys($data);

        return $this->newQuery()->insert($columns)
            ->into($table)
            ->values($data);
    }

    /**
     * Create a 'delete' query for the given table.
     *
     * @param string $table the table to delete from
     *
     * @return Query A new delete query
     */
    public function newDelete(string $table): Query
    {
        return $this->newQuery()->delete($table);
    }

    /**
     * Before update event.
     *
     * @param callable $callback The callback (string $row, string $table)
     *
     * @return void
     */
    public function beforeUpdate(callable $callback): void
    {
        $this->beforeUpdateCallback = $callback;
    }

    /**
     * Before insert event.
     *
     * @param callable $callback The callback (string $row, string $table)
     *
     * @return void
     */
    public function beforeInsert(callable $callback): void
    {
        $this->beforeInsertCallback = $callback;
    }
}
