<?php

namespace App\Utility;

use Aura\SqlQuery\QueryFactory as Query;
use PDO;

/**
 * Database object container
 */
class Database
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var Query
     */
    protected $query;

    /**
     * Database constructor.
     * @param PDO $pdo
     * @param Query $query
     */
    public function __construct(PDO $pdo, Query $query)
    {
        $this->pdo = $pdo;
        $this->query = $query;
    }

    /**
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }
}
