<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Framekit\Event;
use Framekit\Events\AggregateCreated;
use Tests\UnitCase;

/**
 * AggregateCreated unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class AggregateCreatedTest extends UnitCase
{
    public function testEventWithDate()
    {
        $date  = Carbon::now();
        $event = new AggregateCreated('test', $date);

        $this->assertEquals(
            $date,
            $event->createdAt
        );

        $this->assertEquals(
            'test',
            $event->aggregateId
        );
    }
}
