<?php

namespace App\Model;

/**
 * Domain Model Interface.
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
