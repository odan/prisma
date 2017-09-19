<?php

namespace App\Table;

use App\Entity\EntityInterface;
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
abstract class BaseTable implements TableInterface
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
    protected $table = null;

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
        return $this->db->newQuery()->from($this->table);
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
     * @param EntityInterface $entity The entity
     * @return StatementInterface Statement
     */
    public function insert(EntityInterface $entity)
    {
        return $this->db->insert($this->table, $entity->toArray());
    }

    /**
     * Update of an entity's data in the table.
     *
     * @param EntityInterface $entity The entity
     * @param array|null $data The actual data that needs to be saved
     * @return bool Success
     * @throws Exception On error
     */
    public function update(EntityInterface $entity, $data = null): bool
    {
        if (empty($entity->id)) {
            throw new Exception(__('The entity [id] is not defined'));
        }
        if ($data === null) {
            $data = $entity->toArray();
        }

        $query = $this->db->newQuery()->update($this->table)->set($data);
        $statement = $query->where(['id' => $entity->id])->execute();
        $success = $statement->errorCode() === '00000';
        $statement->closeCursor();

        return $success;
    }

    /**
     * Update all rows for the matching key value identifiers with the given data.
     *
     * @param array|EntityInterface $fields Row data
     * @param string|array|\Cake\Database\ExpressionInterface|callable|null $conditions Id or where condition
     * @return int Row count
     */
    public function updateAll($fields, $conditions = null)
    {
        if ($fields instanceof EntityInterface) {
            $fields = $fields->toArray();
        }

        $query = $this->db->newQuery()->update($this->table)->set($fields);

        if (is_numeric($conditions) && ctype_digit(strval($conditions))) {
            $query->where(['id' => $conditions]);
        } else {
            $query->where($conditions);
        }

        $statement = $query->execute();
        $statement->closeCursor();

        return $statement->rowCount();
    }

    /**
     * Delete a single entity.
     *
     * @param EntityInterface $entity
     * @return bool Success
     * @throws Exception On error
     */
    public function delete(EntityInterface $entity)
    {
        if (empty($entity->id)) {
            throw new Exception(__('The entity [id] is not defined'));
        }

        $statement = $this->db->newQuery()->delete($this->table)->where(['id' => $entity->id])->execute();
        $success = $statement->errorCode() === '00000';

        return $success;
    }

    /**
     * Delete all rows of a table matching the given identifier, where keys are column names.
     *
     * @param string|array|\Cake\Database\ExpressionInterface|callable|null $conditions Id or where condition
     * @return int The number of rows affected by the statement
     */
    public function deleteAll($conditions = null)
    {
        $query = $this->db->newQuery()->delete()->where($conditions);
        $statement = $query->execute();
        $statement->closeCursor();

        return $statement->rowCount();
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
