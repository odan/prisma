<?php

namespace App\Repository;

use App\Table\TableInterface;

/**
 * Repository
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var mixed
     */
    protected $table;
}
