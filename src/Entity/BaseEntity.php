<?php

/**
 * While Table Objects represent and provide access to a collection of objects,
 * entities represent individual rows or domain objects in your application.
 *
 * Entities are just value objects which contains no methods to manipulate the database.
 */

namespace App\Entity;

use Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Zend\Hydrator\ObjectProperty as Hydrator;

/**
 * Class BaseEntity
 */
class BaseEntity
{

    /**
     * Hydrator
     *
     * @var Hydrator
     */
    static $hydrator = null;
    
    /**
     * Constructor.
     *
     * BaseEntity constructor.
     * @param array|null $row
     */
    public function __construct(array $row = null)
    {
        if(!static::$hydrator) {
            static::$hydrator = (new Hydrator())->setNamingStrategy(new UnderscoreNamingStrategy());
        }
        if ($row) {
            static::$hydrator->hydrate($row, $this);
        }
    }

    /**
     * Convert to array.
     *
     * @return array Data
     */
    public function toArray()
    {
        return (new Hydrator())->setNamingStrategy(new UnderscoreNamingStrategy())->extract($this);
    }

    /**
     * Convert to json.
     *
     * @param int $options Options
     * @return string A json string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
