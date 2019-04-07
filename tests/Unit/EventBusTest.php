<?php

namespace Tests\Unit;

use Tests\UnitCase;

use Framekit\Contracts\Bus;
use Framekit\Drivers\EventBus;
use Illuminate\Foundation\Application;

/**
 * EventBus unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class EventBusTest extends UnitCase
{
    public function testClassResolveContract()
    {
        $this->assertInstanceOf(
            Bus::class,
            new EventBus($this->createMock(Application::class))
        );
    }

    public function testRegisterHandlerViaConstructor()
    {
        $bus = new EventBus($this->createMock(Application::class), [
            'from' => 'to'
        ]);

        $this->assertEquals(
            ['from' => 'to'],
            $bus->handlers()
        );
    }

    public function testRegisterHandlers()
    {
        $bus = new EventBus($this->createMock(Application::class));

        $this->assertTrue(!$bus->handlers());

        $bus->register(['from' => 'to']);

        $this->assertEquals(
            ['from' => 'to'],
            $bus->handlers()
        );
    }

    public function testReplaceMethod()
    {
        $bus = new EventBus($this->createMock(Application::class), [
            'from' => 'to'
        ]);

        $this->assertEquals(
            ['from' => 'to'],
            $bus->handlers()
        );

        $bus->replace([
            'from2' => 'to2'
        ]);

        $this->assertEquals(
            ['from2' => 'to2'],
            $bus->handlers()
        );
    }

    public function testRegisterGlobals()
    {
        $bus = new EventBus($this->createMock(Application::class));

        $this->assertEquals(
            [],
            $bus->globalHandlers()
        );

        $bus->registerGlobals([
            'Class1',
            'Class2'
        ]);

        $this->assertEquals(
            ['Class1', 'Class2'],
            $bus->globalHandlers()
        );

        $bus->registerGlobals(['Class3']);

        $this->assertEquals(
            ['Class1', 'Class2', 'Class3'],
            $bus->globalHandlers()
        );
    }
}
