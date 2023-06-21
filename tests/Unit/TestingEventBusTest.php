<?php

namespace Tests\Unit;

use Framekit\Testing\EventBus;
use Tests\Components\IntegerAdded;
use Tests\NonPublicMethodTool;
use Tests\UnitCase;

/**
 * Testing\EventBus unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class TestingEventBusTest extends UnitCase
{
    use NonPublicMethodTool;

    public function testPublishingEvents()
    {
        $bus = new EventBus([
            IntegerAdded::class => 'DummyReactor'
        ]);
        $bus->publish(new IntegerAdded('test', 2));

        $this->assertEquals(
            [
                IntegerAdded::class => ['DummyReactor']
            ],
            $bus->published()
        );
    }

    public function testIsCalledPossitive()
    {
        $bus = new EventBus([
            IntegerAdded::class => 'DummyReactor'
        ]);
        $bus->publish(new IntegerAdded('test', 2));

        $compose = self::getMethodOfClass(EventBus::class, 'isCalled');

        $this->assertTrue(
            $compose->invokeArgs($bus, [
                IntegerAdded::class,
                'DummyReactor'
            ])
        );
    }

    public function testIsCalledNegative()
    {
        $bus = new EventBus([
            IntegerAdded::class => 'DummyReactor'
        ]);

        $compose = self::getMethodOfClass(EventBus::class, 'isCalled');

        $this->assertFalse(
            $compose->invokeArgs($bus, [
                IntegerAdded::class,
                'DummyReactorBad'
            ])
        );
    }
}
