<?php

namespace App\Utility;

use ArrayIterator;

/**
 * Class Collection
 */
class Collection extends ArrayIterator
{
    /**
     * The each method iterates over the items in the
     * collection and passes each item to a callback.
     *
     * @param callable $func
     * @return $this
     */
    public function each(callable $func)
    {
        foreach ($this as $key => $item) {
            $func($item, $key);
        }
        return $this;
    }

    /**
     * The map method iterates through the collection and passes each value to the given callback.
     * The callback is free to modify the item and return it, thus forming a new collection of modified items:
     *
     * @param callable $func
     * @return $this
     */
    public function map(callable $func)
    {
        foreach ($this as $key => $item) {
            $this[$key] = $func($item, $key);
        }
        return $this;
    }

    /**
     * The filter method filters the collection using the given callback,
     * keeping only those items that pass a given truth test:
     *
     * $filtered = $collection->filter(function ($value, $key) {
     *     return $value > 2;
     * });
     *
     * @param callable $func
     * @return self
     */
    public function filter(callable $func)
    {
        foreach ($this as $key => $value) {
            if (!$func($value, $key)) {
                unset($this[$key]);
            }
        }
        return $this;
    }

    /**
     * The reduce method reduces the collection to a single value,
     * passing the result of each iteration into the subsequent iteration:
     *
     * $collection = new Collection([1, 2, 3]);
     *
     * // 6
     * $total = $collection->reduce(function ($carry, $item) {
     *    return $carry + $item;
     * });
     *
     * @param callable $func
     * @param mixed $carry
     * @return mixed Total
     */
    public function reduce(callable $func, $carry = null)
    {
        foreach ($this as $key => $item) {
            $carry = $func($carry, $item, $key);
        }
        return $carry;
    }
}
