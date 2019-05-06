<?php

namespace Tests\Unit;

use Tests\UnitCase;

use Carbon\Carbon;
use Framekit\Event;
use Framekit\Events\AggregateRemoved;

/**
 * AggregateRemoved unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class AggregateRemovedTest extends UnitCase
{
    public function testClassResolveContract()
    {
        $this->assertInstanceOf(
            Event::class,
            new AggregateRemoved('test')
        );
    }

    public function testEventWithDate()
    {
        $date  = Carbon::now();
        $event = new AggregateRemoved('test', $date);

        $this->assertEquals(
            $date,
            $event->deletedAt
        );

        $this->assertEquals(
            'test',
            $event->aggregateId
        );
    }

    public function testEventWithoutDate()
    {
        $event = new AggregateRemoved('test');

        $this->assertEquals(
            Carbon::now()->toDateTimeString(),
            $event->deletedAt->toDateTimeString()
        );
    }
}
