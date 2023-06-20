<?php

namespace Tests\Components;

use Carbon\Carbon;
use Framekit\Event;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class DateAdded extends Event
{
    /**
     * @var \Carbon\Carbon
     */
    public Carbon $date;

    /**
     * @param string         $id
     * @param \Carbon\Carbon $date
     */
    public function __construct(string $id, Carbon $date)
    {
        parent::__construct();
        $this->aggregateId = $id;

        $this->date = $date;
    }
}
