<?php

namespace App\Table;

use Cake\Database\Connection;
use Cake\Database\Query;
use Cake\Database\StatementInterface;

/**
 * Base Repository
 */
class BaseTable
{
    /**
     * Connection
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
     * Create a new Query instance for this table.
     *
     * @return Query
     */
    public function getQuery()
    {
        return $this->db->newQuery()->from($this->table);
    }

    /**
     * Find row by id.
     *
     * @param int $id
     *
     * @return array $row with data from database
     */
    public function findById($id)
    {
        $query = $this->getQuery();
        $query->select('*')->where(['id' => $id]);
        return $query->execute()->fetch('assoc');
    }

    /**
     * Returns an array with all rows.
     *
     * @return array $rows
     */
    public function getAll()
    {
        $query = $this->getQuery()->select('*');
        return $query->execute()->fetchAll('assoc');
    }

    /**
     * Insert into database.
     *
     * @param array $row Row data
     * @return StatementInterface
     */
    public function insert($row)
    {
        return $this->db->insert($this->table, $row);
    }

    /**
     * Update a row.
     *
     * @param int $id Id
     * @param array $row Row data
     * @return StatementInterface
     */
    public function update($id, $row)
    {
        $query = $this->db->newQuery();
        $query->update($this->table)->set($row)->where(['id' => $id]);
        return $query->execute();
    }

    /**
     * Delete a row.
     *
     * @param int $id Id
     * @return bool true on success, false otherwise
     */
    public function delete($id)
    {
        return (bool)$this->db->delete($this->table, ['id' => $id]);
    }
}
