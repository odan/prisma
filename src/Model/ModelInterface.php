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
     * @return array Data
     */
    public function toArray(): array;
}
