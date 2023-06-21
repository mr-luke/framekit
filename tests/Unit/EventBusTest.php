<?php

namespace Tests\Unit;

use Framekit\Drivers\EventBus;
use Framekit\Exceptions\MissingReactor;
use Mrluke\Bus\Exceptions\InvalidHandler;
use Mrluke\Bus\Process;
use Tests\Components\DummyProjection;
use Tests\Components\DummyReactor;
use Tests\Components\IntegerAdded;
use Tests\UnitCase;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class EventBusTest extends UnitCase
{
    public function testMapGlobals()
    {
        $bus = $this->getMockBuilder(EventBus::class)
            ->onlyMethods(['createProcess', 'processHandlersStack'])
            ->disableOriginalConstructor()
            ->getMock();

        $bus->mapGlobals([DummyReactor::class]);

        $this->assertEquals(
            [DummyReactor::class],
            $bus->globalReactors()
        );
    }

    public function testThrowWhenGlobalReactorIsNotAReactor()
    {
        $this->expectException(InvalidHandler::class);

        $bus = $this->getMockBuilder(EventBus::class)
            ->onlyMethods(['createProcess', 'processHandlersStack'])
            ->disableOriginalConstructor()
            ->getMock();

        $bus->mapGlobals([EntityTest::class]);
    }

    public function testIfReturnsNullWhenNoReactor()
    {
        $bus = $this->getMockBuilder(EventBus::class)
            ->onlyMethods(['createProcess', 'processHandlersStack'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertNull(
            $bus->publish(new IntegerAdded('test', 1))
        );
    }

    public function testThrowsWhenNoReactor()
    {
        $this->expectException(MissingReactor::class);

        $bus = $this->getMockBuilder(EventBus::class)
            ->onlyMethods(['createProcess', 'processHandlersStack'])
            ->disableOriginalConstructor()
            ->getMock();

        $bus->throwWhenNoHandler = true;
        $bus->publish(new IntegerAdded('test', 1));
    }

    public function testIfGlobalReactorsAreExecutedForAnEvent()
    {
        $bus = $this->getMockBuilder(EventBus::class)
            ->onlyMethods(['createProcess', 'processHandlersStack'])
            ->disableOriginalConstructor()->getMock();

        $handlersStack = [DummyProjection::class, DummyReactor::class];
        $process = Process::create('bus', IntegerAdded::class, $handlersStack);

        $event = new IntegerAdded('test', 1);

        $bus->expects($this->once())
            ->method('createProcess')
            ->with($event, $handlersStack)
            ->willReturn($process);

        $bus->expects($this->once())->method('processHandlersStack');

        $bus->mapGlobals([DummyProjection::class]);
        $bus->map([IntegerAdded::class => [DummyReactor::class]]);

        $this->assertEquals(
            $process,
            $bus->publish($event)
        );
    }
}
