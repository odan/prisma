<?php

namespace App\Table;

use Cake\Database\Connection;
use Cake\Database\Query;
use Cake\Database\StatementInterface;
use Exception;

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
     * @return Query The query instance
     */
    public function newQuery()
    {
        return $this->db->newQuery()->from($this->tableName);
    }

    /**
     * Fetch row by id.
     *
     * @param int|string $id The ID
     * @return array|false The row
     */
    public function fetchById($id)
    {
        return $this->newQuery()->select('*')->where(['id' => $id])->execute()->fetch('assoc');
    }

    /**
     * Returns an array with all entities.
     *
     * @return array Array with rows
     */
    public function fetchAll()
    {
        return $this->newQuery()->select('*')->execute()->fetchAll('assoc');
    }

    /**
     * Insert a row into the given table name using the key value pairs of data.
     *
     * @param array $data The row data
     * @return StatementInterface Statement
     */
    public function insert(array $data)
    {
        return $this->db->insert($this->tableName, $data);
    }

    /**
     * Update of a single row in the table.
     *
     * @param array $row The actual row data that needs to be saved
     * @param int|string|array $conditions Id or conditions
     * @return StatementInterface Statement
     */
    public function update(array $row, $conditions): bool
    {
        if (!is_array($conditions)) {
            $conditions = ['id' => $conditions];
        }

        return $this->db->update($this->tableName, $row, $conditions);
    }

    /**
     * Delete a single entity.
     *
     * @param int|array $conditions ID or conditions
     * @return StatementInterface Statement
     */
    public function delete($conditions)
    {
        if (!is_array($conditions)) {
            $conditions = ['id' => $conditions];
        }
        return $this->db->delete($this->tableName, $conditions);
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @return int|string The row ID of the last row that was inserted into the database.
     */
    public function lastInsertId()
    {
        return $this->db->getDriver()->lastInsertId();
    }
}
