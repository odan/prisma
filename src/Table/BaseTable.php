<?php

namespace App\Table;

use Cake\Database\Connection;
use Cake\Database\Query;
use Cake\Database\StatementInterface;
use Exception;

/**
 * Base Repository
 */
class BaseTable
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
     * @return Query
     */
    protected function newQuery()
    {
        return $this->db->newQuery()->from($this->table);
    }

    /**
     * Returns an array with all rows.
     *
     * @return array $rows
     */
    public function findAll()
    {
        return $this->newQuery()->select('*')->execute()->fetchAll('assoc');
    }

    /**
     * Find row by id.
     *
     * @param int $id
     * @return array|false $row with data from database
     */
    public function findById($id)
    {
        return $this->newQuery()->select('*')->where(['id' => $id])->execute()->fetch('assoc');
    }

    /**
     * Insert a row into the given table name using the key value pairs of data.
     *
     * @param array $row Row data
     * @return StatementInterface Statement
     * @throws Exception On error
     */
    public function insert($row)
    {
        return $this->db->insert($this->table, $row);
    }

    /**
     * Update all rows for the matching key value identifiers with the given data.
     *
     * @param array $row Row data
     * @param int|array $where Id or where condition
     * @return StatementInterface Statement
     */
    public function update($row, $where)
    {
        $query = $this->db->newQuery()->update($this->table)->set($row);
        if ($this->isInteger($where)) {
            $query->where(['id' => $where]);
        } else {
            $query->where($where);
        }
        return $query->execute();
    }

    /**
     * Delete all rows of a table matching the given identifier, where keys are column names.
     *
     * @param int|array $where Id or where condition
     * @return StatementInterface Statement
     * @throws Exception On error
     */
    public function delete($where)
    {
        $query = $this->db->newQuery()->delete($this->table);
        if ($this->isInteger($where)) {
            $query->where(['id' => $where]);
        } else {
            $query->where($where);
        }
        return $query->execute();
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @param string $table Table [optional]
     * @param string $column Column [optional]
     * @return string The row ID of the last row that was inserted into the database.
     */
    public function lastInsertId($table = null, $column = null)
    {
        return $this->db->getDriver()->lastInsertId($table, $column);
    }

    /**
     * Is big integer.
     *
     * @param mixed $value Value
     * @return bool Status
     */
    protected function isInteger($value)
    {
        return (is_numeric($value) && ctype_digit(strval($value)));
    }
}
