<?php

namespace Tests\Unit;

use Tests\UnitCase;

use Carbon\Carbon;
use Framekit\Event;
use Framekit\Events\AggregateCreated;

/**
 * AggregateCreated unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class AggregateCreatedTest extends UnitCase
{
    public function testClassResolveContract()
    {
        $this->assertInstanceOf(
            Event::class,
            new AggregateCreated('test')
        );
    }

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
            $event->aggreagateId
        );
    }

    public function testEventWithoutDate()
    {
        $event = new AggregateCreated('test');

        $this->assertEquals(
            Carbon::now()->toDateTimeString(),
            $event->createdAt->toDateTimeString()
        );
    }
}
