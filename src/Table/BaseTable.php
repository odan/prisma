<?php

namespace App\Table;

use App\Utility\Database;
use Exception;
use FluentPDO;
use PDO;
use PDOStatement;
use SelectQuery;

/**
 * Base Repository
 */
class BaseTable
{
    /**
     * Connection
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * Query Builder
     *
     * @var FluentPDO
     */
    protected $query;

    /**
     * Table name
     *
     * @var string|null
     */
    protected $table = null;

    /**
     * Constructor
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->pdo = $db->getPdo();
        $this->query = $db->getQuery();
    }

    /**
     * Create a new select query instance for this table.
     *
     * @return SelectQuery
     */
    protected function newSelect()
    {
        return $this->query->from($this->table);
    }

    /**
     * Returns an array with all rows.
     *
     * @return array $rows
     */
    public function findAll()
    {
        return $this->newSelect()->fetchAll();
    }

    /**
     * Find row by id.
     *
     * @param int $id
     *
     * @return array|false $row with data from database
     */
    public function findById($id)
    {
        return $this->newSelect()->where('id', $id)->fetch();
    }

    /**
     * Insert into database.
     *
     * @param array $row Row data
     * @return string Last inserted id
     * @throws Exception On error
     */
    public function insert($row)
    {
        return $this->query->insertInto($this->table, $row)->execute();
    }

    /**
     * Update a row.
     *
     * @param array $row Row data
     * @param int $id Id
     * @return PDOStatement
     */
    public function update($row, $id)
    {
        return $this->query->update($this->table, $row)->where($id)->execute();
    }

    /**
     * Delete a row by id.
     *
     * @param int|string $id Id
     * @return int Number of affected rows
     * @throws Exception On error
     */
    public function delete($id)
    {
        $result = $this->query->deleteFrom($this->table, $id)->execute();
        if ($result === false) {
            throw new Exception(__('Delete failed'));
        }
        return $result;
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @param string $name [optional]
     * Name of the sequence object from which the ID should be returned.
     * @return string The row ID of the last row that was inserted into the database.
     */
    public function lastInsertId($name = null)
    {
        return $this->query->getPdo()->lastInsertId($name);
    }
}
