<?php

namespace Tests\Components;

use Carbon\Carbon;
use Framekit\Event;

/**
 * Test Event.
 *
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
    public $date;

    /**
     * @param \Carbon\Carbon $date
     */
    public function __construct(Carbon $date)
    {
        parent::__construct();

        $this->date = $date;
    }
}
