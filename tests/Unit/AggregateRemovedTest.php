<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Framekit\Event;
use Framekit\Events\AggregateRemoved;
use Tests\UnitCase;

/**
 * AggregateRemoved unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class AggregateRemovedTest extends UnitCase
{
    public function testEventWithDate()
    {
        $this->assertInstanceOf(
            Event::class,
            new AggregateRemoved('test', Carbon::now())
        );
    }
}
