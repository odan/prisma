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
     * @return array|null The row
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
     * @param array|null $row The row data. An associative array containing column-value pairs.
     * @return InsertQuery The insert query object.
     */
    public function insert($row = null): InsertQuery
    {
        $insert = $this->db->insert()->into($this->tableName);
        if ($row) {
            $insert->set($row);
        }
        return $insert;
    }

    /**
     * Executes an SQL UPDATE statement on a table.
     *
     * @param array $row The actual row data that needs to be saved. An associative array containing column-value pairs.
     * @param int|string|array $conditions The ID or the WHERE conditions as associative array containing column-value pairs.
     * @return UpdateQuery The update query object
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
     * Executes an SQL DELETE statement on a table.
     *
     * @param int|array $conditions The ID or the WHERE conditions as associative array containing column-value pairs.
     * @return DeleteQuery The delete query object.
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
