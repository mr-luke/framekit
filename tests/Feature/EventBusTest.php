<?php

namespace Tests\Feature;

use Framekit\Drivers\EventBus;
use Framekit\Event;
use Framekit\Exceptions\UnsupportedEvent;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Tests\Components\DummyReactor;
use Tests\Components\IntegerAdded;
use Tests\FeatureCase;
use Tests\NonPublicMethodTool;

/**
 * EventBus feature tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
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

        $compose = self::getMethodOfClass(EventBus::class, 'fireEventReactors');
        $compose->invokeArgs($bus, [new IntegerAdded(2), 'Tests\Components\ResolveTest']);
    }

    public function testFireEventReactorsMethod()
    {
        $eventMock = $this->getMockBuilder(Event::class)
                          ->setMethods(['dummy'])
                          ->getMockForAbstractClass();

        $eventMock->expects($this->once())
                  ->method('dummy');

        $bus = new EventBus($this->createMock(Application::class));

        $compose = self::getMethodOfClass(EventBus::class, 'fireEventReactors');
        $compose->invokeArgs($bus, [$eventMock, 'Tests\Components\DummyReactor']);
    }

    public function testFireEventReactorsMethodWithMultipleReactors()
    {
        $eventMock = $this->getMockBuilder(Event::class)
                          ->setMethods(['dummy'])
                          ->getMockForAbstractClass();

        $eventMock->expects($this->exactly(3))
                  ->method('dummy');

        $bus = new EventBus($this->createMock(Application::class));

        $compose = self::getMethodOfClass(EventBus::class, 'fireEventReactors');
        $compose->invokeArgs($bus, [$eventMock, [
            'Tests\Components\DummyReactor',
            'Tests\Components\DummyReactor',
            'Tests\Components\DummyReactor',
        ]]);
    }

    public function testValidateReactors()
    {
        $eventMock = $this->getMockBuilder(Event::class)
                          ->setMethods(['dummy'])
                          ->getMockForAbstractClass();

        $wrongReactor = $this->getMockBuilder('WrongReactor')->getMock();

        $this->expectException(UnsupportedEvent::class);

        $bus = new EventBus($this->createMock(Application::class));

        $compose  = self::getMethodOfClass(EventBus::class, 'fireEventReactors');
        $reactors = [
            get_class($wrongReactor),
        ];

        $compose->invokeArgs($bus, [$eventMock, $reactors]);
    }

    public function testPublishMethod()
    {
        $eventMock = $this->getMockBuilder(Event::class)
                          ->setMethods(['dummy'])
                          ->getMockForAbstractClass();

        $eventMock->expects($this->once())
                  ->method('dummy');

        $bus = new EventBus($this->createMock(Application::class), [
            get_class($eventMock) => DummyReactor::class,
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
            get_class($eventMock) => DummyReactor::class,
        ], [
            get_class($eventMock) => DummyReactor::class,
        ]);

        $bus->publish($eventMock);
    }
}
