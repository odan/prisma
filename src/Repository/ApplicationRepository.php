<?php

namespace App\Repository;

use App\Service\User\Auth;
use Cake\Database\Connection;
use Cake\Database\Query;
use RuntimeException;

/**
 * Repository (persistence oriented).
 */
abstract class ApplicationRepository implements RepositoryInterface
{
    /**
     * Connection.
     *
     * @var Connection
     */
    protected $db;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * Constructor.
     *
     * @param Connection $db
     * @param Auth $auth
     */
    public function __construct(Connection $db, Auth $auth)
    {
        $this->db = $db;
        $this->auth = $auth;
    }

    /**
     * Create a new query.
     *
     * @return Query
     */
    protected function newQuery(): Query
    {
        return $this->db->newQuery();
    }

    /**
     * Create a new select query.
     *
     * @param string $table The table name
     * @param string|null $alias The table alias
     *
     * @return Query A select query
     */
    protected function newSelect(string $table, string $alias = null): Query
    {
        $tables = $table;

        if ($alias !== null) {
            $tables = [$alias => $table];
        }

        $query = $this->newQuery()->from($tables);

        if ($query instanceof Query) {
            return $query;
        }

        throw new RuntimeException('New select query failed');
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
            $data['updated_at'] = now();
        }

        if (!isset($data['updated_by'])) {
            $data['updated_by'] = $this->auth->getId();
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
            $data['created_at'] = now();
        }

        if (!isset($data['created_by'])) {
            $data['created_by'] = $this->auth->getId();
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
     * @param int|string $id ID
     *
     * @return array Result set
     */
    protected function fetchById(string $table, $id): array
    {
        $result = $this->newSelect($table)->select('*')
            ->where(['id' => $id])
            ->execute()
            ->fetch('assoc');

        return $result ?: [];
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
        $result = $this->newSelect($table)->select('id')
            ->andWhere(['id' => $id])
            ->execute()
            ->fetch('assoc');

        return $result ? true : false;
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
        $result = $this->newSelect($table)->select('*')->execute()->fetchAll('assoc');

        return $result ?: [];
    }
}
