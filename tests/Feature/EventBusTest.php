<?php

namespace Tests\Feature;

use Tests\FeatureCase;
use Tests\NonPublicMethodTool;

use Framekit\Contracts\Bus;
use Framekit\Contracts\Publishable;
use Framekit\Drivers\EventBus;
use Framekit\Event;
use Framekit\Exceptions\UnsupportedEvent;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/**
 * EventBus feature tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class EventBusTest extends FeatureCase
{
    use NonPublicMethodTool;

    public function testThrowWhenNoReactor()
    {
        $this->expectException(UnsupportedEvent::class);

        $return = $this->createMock(Request::class);

        $appMock = $this->getMockBuilder(Application::class)
                        ->setMethods(['make'])
                        ->getMock();

        $appMock->expects($this->once())
                ->method('make')
                ->with($this->equalTo('Illuminate\Http\Request'))
                ->willReturn($return);

        $bus = new EventBus($appMock);

        $compose = self::getMethodOfClass(EventBus::class, 'fireEventReactor');
        $compose->invokeArgs($bus, [new \Tests\Components\IntegerAdded(2), 'Tests\Components\ResolveTest']);
    }

    public function testFireEventReactorMethod()
    {
        $eventMock = $this->getMockBuilder(Event::class)
                          ->setMethods(['dummy'])
                          ->getMockForAbstractClass();

        $eventMock->expects($this->once())
                  ->method('dummy');

        $bus = new EventBus($this->createMock(Application::class));

        $compose = self::getMethodOfClass(EventBus::class, 'fireEventReactor');
        $compose->invokeArgs($bus, [$eventMock, 'Tests\Components\DummyReactor']);
    }

    public function testPublishMethod()
    {
        $eventMock = $this->getMockBuilder(Event::class)
                          ->setMethods(['dummy'])
                          ->getMockForAbstractClass();

        $eventMock->expects($this->once())
                  ->method('dummy');

        $bus = new EventBus($this->createMock(Application::class), [
            get_class($eventMock) => \Tests\Components\DummyReactor::class
        ]);
        $bus->publish($eventMock);
    }

    public function testPublishWithGlobal()
    {
        $eventMock = $this->getMockBuilder(Event::class)
                          ->setMethods(['dummy'])
                          ->getMockForAbstractClass();

        $eventMock->expects($this->exactly(2))
                  ->method('dummy');

        $bus = new EventBus($this->createMock(Application::class), [
            get_class($eventMock) => \Tests\Components\DummyReactor::class
        ], [
            get_class($eventMock) => \Tests\Components\DummyReactor::class
        ]);

        $bus->publish($eventMock);
    }
}
