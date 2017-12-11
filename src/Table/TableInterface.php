<?php

namespace App\Table;

use Illuminate\Database\Query\Builder;

/**
 * The Table Gateway Interface
 */
interface TableInterface
{
    /**
     * Return the table name.
     *
     * @return string The table name
     */
    public function getTable(): string;

    /**
     * Return a new Query Builder instance.
     *
     * @return Builder The Query Builder
     */
    public function newQuery(): Builder;
}
