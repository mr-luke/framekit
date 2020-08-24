<?php

namespace Tests\Feature;

use Tests\Components\IntegerAdded;
use Tests\FeatureCase;
use Tests\Components\TestAggregate;

use Carbon\Carbon;
use Framekit\Events\AggregateCreated;
use Framekit\Exceptions\MethodUnknown;

/**
 * EventSourcedAggregate feature tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class EventSourcedAggregateTest extends FeatureCase
{
    public function testCreateAggreaget()
    {
        $aggregate = TestAggregate::create('uuid');

        $events = $aggregate->getUncommitedEvents();

        $this->assertEquals(1, count($events));

        $this->assertInstanceOf(
            AggregateCreated::class,
            $events[0]
        );
    }

    public function testRecreateFromStream()
    {
        $event = new AggregateCreated('uuid', Carbon::now());

        $aggregate = TestAggregate::recreateFromStream('uuid', [$event]);

        $this->assertEquals(
            [],
            $aggregate->getUncommitedEvents()
        );

        $this->assertEquals(1, $aggregate->getVersion());
    }

    public function testRecreatingFromStreamSkipEvents()
    {
        $event = new IntegerAdded(5);

        $aggregate = TestAggregate::recreateFromStream('uuid', [$event], true);

        $this->assertEquals(
            [],
            $aggregate->getUncommitedEvents()
        );

        $this->assertEquals(0, $aggregate->getVersion());
    }

    public function testRecreatingFromStreamThrowsExceptionOnUnknownEvent()
    {
        $this->expectException(MethodUnknown::class);
        $event = new IntegerAdded(5);

        TestAggregate::recreateFromStream('uuid', [$event], false);
    }
}
