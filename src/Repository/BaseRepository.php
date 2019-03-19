<?php

namespace App\Repository;

use App\Domain\User\Auth;
use Cake\Chronos\Chronos;
use Cake\Database\Connection;
use Cake\Database\Query;
use RuntimeException;

/**
 * Repository (persistence oriented).
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * Connection.
     *
     * @var Connection
     */
    protected $connection;

    /**
     * @var Auth|null
     */
    protected $auth;

    /**
     * Constructor.
     *
     * @param Connection $connection
     * @param Auth|null $auth
     */
    public function __construct(Connection $connection, Auth $auth = null)
    {
        $this->connection = $connection;
        $this->auth = $auth;
    }

    /**
     * Create a new query.
     *
     * @return Query
     */
    protected function newQuery(): Query
    {
        return $this->connection->newQuery();
    }

    /**
     * Create a new select query.
     *
     * @param string $table The table name
     *
     * @return Query A select query
     */
    protected function newSelect(string $table): Query
    {
        $query = $this->newQuery()->from($table);

        if (!$query instanceof Query) {
            throw new RuntimeException('Failed to create query');
        }

        return $query;
    }

    /**
     * Executes an UPDATE statement on the specified table.
     *
     * @param string $table the table to update rows from
     * @param array $data values to be updated [optional]
     *
     * @return Query Query
     */
    protected function newUpdate(string $table, array $data = []): Query
    {
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = Chronos::now()->toDateTimeString();
        }

        if ($this->auth !== null && !isset($data['updated_user_id'])) {
            $data['updated_user_id'] = $this->auth->getUserId();
        }

        return $this->newQuery()->update($table)->set($data);
    }

    /**
     * Executes an UPDATE statement on the specified table.
     *
     * @param string $table the table to update rows from
     * @param array $data values to be updated
     *
     * @return Query Query
     */
    protected function newInsert(string $table, array $data): Query
    {
        if (!isset($data['created_at'])) {
            $data['created_at'] = Chronos::now()->toDateTimeString();
        }

        if ($this->auth !== null && !isset($data['created_user_id'])) {
            $data['created_user_id'] = $this->auth->getUserId();
        }

        $columns = array_keys($data);

        return $this->newQuery()->insert($columns)
            ->into($table)
            ->values($data);
    }

    /**
     * Create a DELETE query.
     *
     * @param string $table the table to delete from
     *
     * @return Query Query
     */
    protected function newDelete(string $table): Query
    {
        return $this->newQuery()->delete($table);
    }

    /**
     * Fetch row by id.
     *
     * @param string $table Table name
     * @param int $id ID
     *
     * @return array Result set
     */
    protected function fetchById(string $table, int $id): array
    {
        return $this->newSelect($table)
            ->select('*')
            ->where(['id' => $id])
            ->execute()
            ->fetch('assoc') ?: [];
    }

    /**
     * Fetch row by id.
     *
     * @param string $table Table name
     * @param int|string $id ID
     *
     * @return bool True if the row exists
     */
    protected function existsById(string $table, $id): bool
    {
        return $this->newSelect($table)
            ->select('id')
            ->andWhere(['id' => $id])
            ->execute()
            ->fetch('assoc') ? true : false;
    }

    /**
     * Fetch all rows.
     *
     * @param string $table Table name
     *
     * @return array Result set
     */
    protected function fetchAll(string $table): array
    {
        return $this->newSelect($table)->select('*')->execute()->fetchAll('assoc') ?: [];
    }
}
