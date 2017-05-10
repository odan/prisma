<?php

namespace App\Model;

use Cake\Database\Connection;

/**
 * Model: Data Access layer
 *
 * This layer provides access to the persistence layer.
 * This layer is only ever invoked by Service objects.
 * Objects in the data access layer do not know about each other.
 *
 */
class BaseModel
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
    public function __construct($db = null)
    {
        $this->db = $db ?: db();
    }

    protected function getDb() {
        return $this->db;
    }
}
