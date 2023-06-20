<?php

namespace Tests\Components;

use Framekit\Event;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class IntegerAdded extends Event
{
    /**
     * @var int
     */
    public int $toAdd;

    /**
     * @param string $id
     * @param int    $toAdd
     */
    public function __construct(string $id, int $toAdd)
    {
        parent::__construct();
        $this->aggregateId = $id;

        $this->toAdd = $toAdd;
    }
}
