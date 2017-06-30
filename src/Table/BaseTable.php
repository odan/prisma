<?php

namespace App\Table;

use App\Utility\Database;
use Aura\SqlQuery\Mysql\Delete;
use Aura\SqlQuery\Mysql\Insert;
use Aura\SqlQuery\Mysql\Select;
use Aura\SqlQuery\Mysql\Update;
use Aura\SqlQuery\QueryFactory;
use Aura\SqlQuery\QueryInterface;
use PDO;
use PDOStatement;

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
     * @var QueryFactory
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
     * @return Select
     */
    protected function newSelect()
    {
        return $this->query->newSelect()->from($this->table);
    }

    /**
     * Create a new Query instance for this table.
     *
     * @param QueryInterface $query
     * @return PDOStatement
     */
    protected function executeQuery(QueryInterface $query)
    {
        $statement = $this->pdo->prepare($query->getStatement());
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
     * @return Insert
     */
    protected function newInsert()
    {
        return $this->query->newInsert()->into($this->table);
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
        $query->cols($row)->where('id = ?', $id);
        return $this->executeQuery($query);
    }

    /**
     * Create a new update query instance for this table.
     *
     * @return Update
     */
    protected function newUpdate()
    {
        return $this->query->newUpdate()->table($this->table);
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
     * @return Delete
     */
    protected function newDelete()
    {
        return $this->query->newDelete()->from($this->table);
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
        return $this->pdo->lastInsertId($name);
    }
}
