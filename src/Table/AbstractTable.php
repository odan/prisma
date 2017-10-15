<?php

namespace App\Table;

use Odan\Database\Connection;
use Odan\Database\DeleteQuery;
use Odan\Database\InsertQuery;
use Odan\Database\SelectQuery;
use Odan\Database\UpdateQuery;

/**
 * Table Gateways
 *
 * The Table Gateway subcomponent provides an object-oriented representation of a database table;
 * its methods mirror the most common table operations. In code, the interface resembles.
 *
 * Out of the box, this implementation makes no assumptions about table structure or metadata,
 * and when select() is executed, a simple SelectQuery object will be returned.
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
     * Return the table name.
     *
     * @return string The table name
     */
    public function getTable(): string
    {
        return $this->tableName;
    }


    /**
     * Create a new select query instance for this table.
     *
     * @return SelectQuery The query instance
     */
    public function select(): SelectQuery
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
        return $this->select()->columns('*')->where('id', '=', $id)->execute()->fetch();
    }

    /**
     * Returns an array with all entities.
     *
     * @return array Array with rows
     */
    public function fetchAll()
    {
        return $this->select()->columns('*')->execute()->fetchAll();
    }

    /**
     * Insert a row into the given table name using the key value pairs of data.
     *
     * @param array|null $row The row data
     * @return InsertQuery Statement
     */
    public function insert(array $row = null): InsertQuery
    {
        $insert = $this->db->insert()->into($this->tableName);
        if ($row) {
            $insert->set($row);
        }
        return $insert;
    }

    /**
     * Update of a single row in the table.
     *
     * @param array $row The actual row data that needs to be saved
     * @param int|string|array $conditions The ID or the WHERE conditions
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
     * @param int|array $conditions The ID or the WHERE conditions
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
