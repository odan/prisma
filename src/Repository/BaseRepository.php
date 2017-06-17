<?php

namespace App\Repository;

use Cake\Database\Connection;

/**
 * Model: Data Access layer
 *
 * This layer provides access to the persistence layer.
 * This layer is only ever invoked by Service objects.
 * Objects in the data access layer do not know about each other.
 *
 */
class BaseRepository
{
    /**
     * Connection
     *
     * @var Connection
     */
    protected $db;

    /**
     * Constructor
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
}
