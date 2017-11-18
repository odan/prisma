<?php

namespace App\Table;

use Odan\Database\DeleteQuery;
use Odan\Database\InsertQuery;
use Odan\Database\SelectQuery;
use Odan\Database\UpdateQuery;

/**
 * The Table Gateway Interface
 */
interface TableInterface
{
    public function getTable(): string;

    public function select(): SelectQuery;

    public function insert($row = null): InsertQuery;

    public function update(array $row, $conditions): UpdateQuery;

    public function delete($conditions): DeleteQuery;
}
