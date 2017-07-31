<?php

namespace App\Test;

use App\Utility\Collection;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Collection test
 *
 * @coversDefaultClass \App\Utility\Collection
 */
class UtilityCollectionTest extends TestCase
{
    /**
     * Test map function.
     *
     * @covers ::map
     */
    public function testMap()
    {
        $expected = new Collection([
            'key1' => '11',
            'key2' => '21',
            'key3' => '31'
        ]);
        $collection = new Collection([
            'key1' => '1',
            'key2' => '2',
            'key3' => '3'
        ]);
        $collection->map(function ($item) {
            return $item . '1';
        });
        $this->assertEquals($expected, $collection);
    }

    /**
     * Test each function.
     *
     * @covers ::each
     */
    public function testEach()
    {
        $collection = new Collection([
            'key1' => '1',
            'key2' => '2',
            'key3' => '3'
        ]);
        $counter = new stdClass();
        $counter->count = 0;
        $collection->each(function () use ($counter) {
            $counter->count++;
        });
        $this->assertSame($counter->count, 3);
    }

    /**
     * Test filter function.
     *
     * @covers ::filter
     */
    public function testFilter()
    {
        $expected = new Collection();
        $collection = new Collection([
            'key1' => '1',
            'key2' => '2',
            'key3' => '3'
        ]);
        $collection->filter(function () {
            return false;
        });
        $this->assertEquals($expected, $collection);
    }

    /**
     * Test reduce function.
     *
     * @covers ::reduce
     */
    public function testReduce()
    {
        $collection = new Collection([
            'key1' => '1',
            'key2' => '2',
            'key3' => '3'
        ]);
        $result = $collection->reduce(function ($carry, $item) {
            return $item + $carry;
        }, 1);
        $this->assertEquals(7, $result);
    }
}
