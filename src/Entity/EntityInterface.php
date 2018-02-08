<?php

namespace App\Entity;

/**
 * EntityInterface
 */
interface EntityInterface
{

    /**
     * Convert to array.
     *
     * @return array Data
     */
    public function toArray(): array;
}
