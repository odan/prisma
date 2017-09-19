<?php

namespace App\Repository;

use App\Table\TableInterface;

/**
 * Repository
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var TableInterface
     */
    protected $table;
}
