<?php

namespace App\Entity;

/**
 * While a Repository represent and provide access to a collection of objects,
 * domain models (entities) represent individual domain objects (rows) in your application.
 *
 * Entities are just value objects which contains no methods to manipulate the database.
 */
interface EntityInterface
{

    /**
     * Convert to array.
     *
     * @return array Data
     */
    public function toArray();
}
