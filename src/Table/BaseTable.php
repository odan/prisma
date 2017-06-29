<?php

namespace App\Table;

use App\Utility\Database;
use Aura\SqlQuery\Common\DeleteInterface;
use Aura\SqlQuery\Common\InsertInterface;
use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\Common\UpdateInterface;
use Aura\SqlQuery\Mysql\Delete;
use Aura\SqlQuery\Mysql\Insert;
use Aura\SqlQuery\Mysql\Select;
use Aura\SqlQuery\Mysql\Update;
use Aura\SqlQuery\QueryInterface;
use PDOStatement;

/**
 * Base Repository
 */
class BaseTable
{
    /**
     * Database
     *
     * @var Database
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
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Returns an array with all rows.
     *
     * @return array $rows
     */
    public function findAll()
    {
        $query = $this->newSelect()->cols(['*']);
        $statement = $this->executeQuery($query);
        return $statement->fetchAll();
    }

    /**
     * Create a new select query instance for this table.
     *
     * @return SelectInterface|Select
     */
    protected function newSelect()
    {
        return $this->db->getQuery()->newSelect()->from($this->table);
    }

    /**
     * Create a new Query instance for this table.
     *
     * @param QueryInterface $query
     * @return PDOStatement
     */
    protected function executeQuery(QueryInterface $query)
    {
        $statement = $this->db->getPdo()->prepare($query->getStatement());
        $statement->execute($query->getBindValues());
        return $statement;
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
        $query = $this->newSelect();
        $query->cols(['*'])->where('id = ?', $id);
        return $this->executeQuery($query)->fetch();
    }

    /**
     * Insert into database.
     *
     * @param array $row Row data
     * @return PDOStatement
     */
    public function insert($row)
    {
        $insert = $this->newInsert()->cols($row);
        return $this->executeQuery($insert);
    }

    /**
     * Create a new insert query instance for this table.
     *
     * @return InsertInterface|Insert
     */
    protected function newInsert()
    {
        return $this->db->getQuery()->newInsert()->into($this->table);
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
        $query = $this->newUpdate();
        $query->cols($row)->where(['id = ?', $id]);
        return $this->executeQuery($query);
    }

    /**
     * Create a new update query instance for this table.
     *
     * @return UpdateInterface|Update
     */
    protected function newUpdate()
    {
        return $this->db->getQuery()->newUpdate()->table($this->table);
    }

    /**
     * Delete a row by id.
     *
     * @param int $id Id
     * @return PDOStatement
     */
    public function delete($id)
    {
        $query = $this->newDelete()->where('id = ?', $id);
        return $this->executeQuery($query);
    }

    /**
     * Create a new delete query instance for this table.
     *
     * @return DeleteInterface|Delete
     */
    protected function newDelete()
    {
        return $this->db->getQuery()->newDelete()->from($this->table);
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
        return $this->db->getPdo()->lastInsertId($name);
    }
}
