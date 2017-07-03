<?php

namespace App\Utility;

use PDO;
use FluentPDO;

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
     * @var FluentPDO
     */
    protected $query;

    /**
     * Database constructor.
     * @param PDO $pdo
     * @param FluentPDO $query
     */
    public function __construct(PDO $pdo, FluentPDO $query)
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
     * @return FluentPDO
     */
    public function getQuery()
    {
        return $this->query;
    }
}
