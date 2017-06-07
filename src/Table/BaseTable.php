<?php

namespace App\Table;

use Cake\Database\Connection;

/**
 * Model: Data Access layer
 *
 * This layer provides access to the persistence layer.
 * This layer is only ever invoked by Service objects.
 * Objects in the data access layer do not know about each other.
 *
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
     * Constructor
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

}
