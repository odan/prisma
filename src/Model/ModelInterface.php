<?php

namespace App\Model;

/**
 * Data Model Interface.
 */
interface ModelInterface
{
    /**
     * Convert to array.
     *
     * @return mixed[] Data
     */
    public function toArray(): array;
}
