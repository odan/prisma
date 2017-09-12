<?php

namespace App\Model;

use Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Zend\Hydrator\ObjectProperty as Hydrator;

/**
 * Class BaseModel
 */
abstract class BaseModel implements ModelInterface
{

    /**
     * The ID.
     *
     * @var string|int
     */
    public $id;

    /**
     * Constructor.
     *
     * BaseEntity constructor.
     * @param array|null $row
     */
    public function __construct(array $row = null)
    {
        if ($row) {
            $this->getHydrator()->hydrate($row, $this);
        }
    }

    /**
     * Return the ID.
     *
     * @return string|int The ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Hydrator.
     *
     * @return Hydrator Hydrator
     */
    protected function getHydrator()
    {
        static $hydrator = null;
        if (!$hydrator) {
            $hydrator = new Hydrator();
            $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        }
        return $hydrator;
    }

    /**
     * Convert to array.
     *
     * @return array Data
     */
    public function toArray()
    {
        return $this->getHydrator()->extract($this);
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
