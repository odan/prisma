<?php

namespace App\Table;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Connection;
use stdClass;

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
     * Return the table name.
     *
     * @return string The table name
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Return a new Query Builder instance.
     *
     * @return Builder The Query Builder
     */
    public function newQuery(): Builder
    {
        return $this->db->table($this->table);
    }

    /**
     * Fetch row by id.
     *
     * @param int|string $id The ID
     * @return stdClass|null The row
     */
    public function fetchById($id)
    {
        return $this->newQuery()->where('id', '=', $id)->first();
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @return string The row ID of the last row that was inserted into the database.
     */
    public function lastInsertId(): string
    {
        return $this->db->getPdo()->lastInsertId();
    }
}
