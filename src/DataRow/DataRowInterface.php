<?php

namespace App\DataRow;

/**
 * While a Repository represent and provide access to a collection of objects,
 * domain models (entities) represent individual domain objects (rows) in your application.
 *
 * Entities are just value objects which contains no methods to manipulate the database.
 */
interface DataRowInterface
{

    /**
     * Convert to array.
     *
     * @return array Data
     */
    public function toArray(): array;
}
