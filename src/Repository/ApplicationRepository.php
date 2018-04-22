<?php

namespace App\Repository;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use stdClass;

/**
 * Repository (persistence oriented).
 */
abstract class ApplicationRepository implements RepositoryInterface
{
    /**
     * Connection.
     *
     * @var Connection
     */
    protected $db;

    /**
     * Constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @return string the row ID of the last row that was inserted into the database
     */
    protected function getLastInsertId(): string
    {
        return $this->db->getPdo()->lastInsertId();
    }

    /**
     * Fetch row by id.
     *
     * @param string $table
     * @param int|string $id The ID
     *
     * @return stdClass|null The row
     */
    protected function fetchById(string $table, $id)
    {
        return $this->newSelect($table)->where('id', '=', $id)->first();
    }

    /**
     * Return a new Query Builder instance.
     *
     * @param string $table The table name (e.g. 'users' or with alias 'users AS u')
     *
     * @return Builder The Query Builder
     */
    protected function newSelect(string $table): Builder
    {
        return $this->db->table($table);
    }

}
