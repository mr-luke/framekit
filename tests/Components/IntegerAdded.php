<?php

namespace Tests\Components;

use Framekit\Event;

/**
 * Test Event.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class IntegerAdded extends Event
{
    /**
     * @var int
     */
    public $toAdd;

    /**
     * @param int $toAdd
     */
    public function __construct(int $toAdd)
    {
        $this->toAdd = $toAdd;
    }
}
