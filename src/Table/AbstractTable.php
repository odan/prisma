<?php

namespace App\Table;

use Odan\Database\Connection;
use Odan\Database\DeleteQuery;
use Odan\Database\InsertQuery;
use Odan\Database\SelectQuery;
use Odan\Database\UpdateQuery;

/**
 * Repositories The Right Way
 *
 * Implement separate database logic functions for all your needs inside
 * the specific repositories, so your service classes/controllers end up looking like this:
 *
 * $user = $userRepository->findByUsername('admin');
 * $users = $userRepository->findAdminIdsCreatedBeforeDate('2016-01-18 19:21:20');
 * $posts = $postRepository->chunkFilledPostsBeforeDate('2016-01-18 19:21:20');
 *
 * This way all the database logic is moved to the specific repository and I can type hint
 * it's returned models. This methodology also results in cleaner easier to read
 * code and further separates your core logic from the ORM / query builder.
 */
abstract class AbstractTable implements TableInterface
{
    /**
     * Database connection
     *
     * @var Connection
     */
    protected $db;

    /**
     * Table name
     *
     * @var string|null
     */
    protected $tableName = null;

    /**
     * Constructor
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Create a new select query instance for this table.
     *
     * @return SelectQuery The query instance
     */
    public function newQuery()
    {
        return $this->db->select()->from($this->tableName);
    }

    /**
     * Fetch row by id.
     *
     * @param int|string $id The ID
     * @return array|false The row
     */
    public function fetchById($id)
    {
        return $this->newQuery()->columns('*')->where('id', '=', $id)->query()->fetch();
    }

    /**
     * Returns an array with all entities.
     *
     * @return array Array with rows
     */
    public function fetchAll()
    {
        return $this->newQuery()->columns('*')->query()->fetchAll();
    }

    /**
     * Insert a row into the given table name using the key value pairs of data.
     *
     * @param array|null $data The row data
     * @return InsertQuery Statement
     */
    public function insert(array $data = null): InsertQuery
    {
        $insert = $this->db->insert()->into($this->tableName);
        if ($data) {
            $insert->set($data);
        }
        return $insert;
    }

    /**
     * Update of a single row in the table.
     *
     * @param array $row The actual row data that needs to be saved
     * @param int|string|array $conditions Id or conditions
     * @return UpdateQuery Statement
     */
    public function update(array $row, $conditions): UpdateQuery
    {
        if (!is_array($conditions)) {
            $conditions = ['id' => $conditions];
        }

        $update = $this->db->update()->table($this->tableName)->set($row);

        foreach ($conditions as $key => $value) {
            $update->where($key, '=', $value);
        }

        return $update;
    }

    /**
     * Delete a single entity.
     *
     * @param int|array $conditions ID or conditions
     * @return DeleteQuery Statement
     */
    public function delete($conditions): DeleteQuery
    {
        if (!is_array($conditions)) {
            $conditions = ['id' => $conditions];
        }

        $delete = $this->db->delete()->from($this->tableName);
        foreach ($conditions as $key => $value) {
            $delete->where($key, '=', $value);
        }

        return $delete;
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @return string The row ID of the last row that was inserted into the database.
     */
    public function lastInsertId(): string
    {
        return $this->db->lastInsertId();
    }
}
