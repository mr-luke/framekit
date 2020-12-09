<?php

namespace Tests\Unit;

use Tests\Components\IntegerAdded;
use Tests\UnitCase;

use Framekit\Extentions\EventSourcedAggregate;

/**
 * EventSourcedAggregate unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class EventSourcedAggregateTest extends UnitCase
{
    public function testVersionControl()
    {
        $mock = $this->getMockForTrait(EventSourcedAggregate::class);

        $this->assertEquals(0, $mock->getVersion());

        $mock->increaseVersion();

        $this->assertEquals(1, $mock->getVersion());
    }

    public function testgetUncommittedEvents()
    {
        $mock = $this->getMockForTrait(EventSourcedAggregate::class);

        $this->assertEquals([], $mock->getUncommittedEvents());
    }

    public function testFireEvent()
    {
        $event = new IntegerAdded(2);

        $mock = $this->getMockBuilder(EventSourcedAggregate::class)
                     ->setMethods(['applyChange', 'increaseVersion'])
                     ->getMockForTrait();

        $mock->expects($this->once())
             ->method('applyChange')
             ->with($this->equalTo($event));

        $mock->expects($this->once())
             ->method('increaseVersion');

        $mock->fireEvent($event);
    }

    public function testUncommitedEventsControl()
    {
        $event = new IntegerAdded(2);

        $mock = $this->getMockBuilder(EventSourcedAggregate::class)
                     ->setMethods(['applyChange'])
                     ->getMockForTrait();

        $mock->fireEvent($event);

        $this->assertEquals(
            [$event],
            $mock->getUncommittedEvents()
        );

        $this->assertEquals([], $mock->getUncommittedEvents());
    }
}
