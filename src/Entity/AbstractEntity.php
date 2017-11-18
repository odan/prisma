<?php

namespace App\Entity;

use Odan\Hydrator\ObjectProperty as Hydrator;

/**
 * Base Entity
 */
abstract class AbstractEntity implements EntityInterface
{

    /**
     * Constructor.
     *
     * BaseEntity constructor.
     * @param array $row
     */
    public function __construct(array $row = null)
    {
        if ($row) {
            $this->getHydrator()->hydrate($row, $this);
        }
    }

    /**
     * Get Hydrator.
     *
     * @return Hydrator Hydrator
     */
    protected function getHydrator(): Hydrator
    {
        static $hydrator = null;
        if (!$hydrator) {
            $hydrator = new Hydrator();
        }
        return $hydrator;
    }

    /**
     * Convert to array.
     *
     * @return array Data
     */
    public function toArray(): array
    {
        return $this->getHydrator()->extract($this);
    }

    /**
     * Convert to json.
     *
     * @param int $options Options
     * @return string A json string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
