<?php

/**
 * While Table Objects represent and provide access to a collection of objects,
 * entities represent individual rows or domain objects in your application.
 *
 * Entities are just value objects which contains no methods to manipulate the database.
 */

namespace App\Entity;

use Zend\Hydrator\ClassMethods as Hydrator;

/**
 * Class BaseEntity
 */
class BaseEntity
{

    /**
     * Constructor.
     *
     * BaseEntity constructor.
     * @param array|null $row
     */
    public function __construct(array $row = null)
    {
        if ($row) {
            (new Hydrator())->hydrate($row, $this);
        }
    }

    /**
     * Convert to array.
     *
     * @return array Data
     */
    public function toArray()
    {
        return (new Hydrator())->extract($this);
    }
}
