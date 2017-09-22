<?php

namespace App\Repository;

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
