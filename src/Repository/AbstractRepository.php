<?php

namespace App\Repository;

use App\Table\TableInterface;

/**
 * Repository
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var TableInterface
     */
    protected $table;
}
