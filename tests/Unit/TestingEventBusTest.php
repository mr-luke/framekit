<?php

namespace Tests\Unit;

use Tests\Components\DummyReactor;
use Tests\Components\IntegerAdded;
use Tests\NonPublicMethodTool;
use Tests\UnitCase;

use Framekit\Contracts\EventBus as Contract;
use Framekit\Testing\EventBus;

/**
 * Testing\EventBus unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class TestingEventBusTest extends UnitCase
{
    use NonPublicMethodTool;

    public function testPublishingEvents()
    {
        $bus = new EventBus([
            IntegerAdded::class => 'DummyReactor'
        ]);
        $bus->publish(new IntegerAdded(2));

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
        $bus->publish(new IntegerAdded(2));

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
