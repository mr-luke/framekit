<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Framekit\Contracts\Bus;
use Framekit\Contracts\Projector;
use Framekit\Contracts\Retrospector as Contract;
use Framekit\Contracts\Store;
use Framekit\Event;
use Framekit\Eventing\Retrospector;
use Framekit\Retrospection;
use InvalidArgumentException;
use Tests\NonPublicMethodTool;
use Tests\UnitCase;

/**
 * Retrospector unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class RetrospectorTest extends UnitCase
{
    use NonPublicMethodTool;

    public function testClassResolveContract()
    {
        $this->assertInstanceOf(
            Contract::class,
            new Retrospector(
                $this->createMock(Bus::class),
                $this->createMock(Store::class),
                $this->createMock(Projector::class)
            )
        );
    }

    public function testThrowWhenIncludeAndExcludeAtOnce()
    {
        $this->expectException(InvalidArgumentException::class);

        $retrospector = new Retrospector(
            $this->createMock(Bus::class),
            $this->createMock(Store::class),
            $this->createMock(Projector::class)
        );

        $mock                = $this->getMockForAbstractClass(Retrospection::class);
        $mock->filterStreams = ['include' => [], 'exclude' => []];

        $retrospector->perform($mock);
    }

    public function testExcludeReactors()
    {
        $handlers = [
            'EventA' => ['Reactor1', 'Reactor2'],
            'EventB' => ['Reactor1', 'Reactor2', 'Reactor3'],
            'EventC' => 'Reactor1',
            'EventD' => 'Reactor4',
        ];
        $map      = [
            'exclude' => ['Reactor1', 'Reactor2'],
        ];
        $handlers = Retrospector::filterReactors($handlers, $map);
        $this->assertEquals([
            'EventB' => ['Reactor3'],
            'EventD' => 'Reactor4',
        ], $handlers);
    }

    public function testIncludeReactors()
    {
        $handlers = [
            'EventA' => ['Reactor1', 'Reactor2'],
            'EventB' => ['Reactor1', 'Reactor2', 'Reactor3'],
            'EventC' => 'Reactor1',
            'EventD' => 'Reactor4',
        ];
        $map      = [
            'include' => ['Reactor1', 'Reactor2'],
        ];
        $handlers = Retrospector::filterReactors($handlers, $map);
        $this->assertEquals([
            'EventA' => ['Reactor1', 'Reactor2'],
            'EventB' => ['Reactor1', 'Reactor2'],
            'EventC' => 'Reactor1',
        ], $handlers);
    }

    public function testExcludeStream()
    {
        $event1 = $this->generateEvent(null, ['stream_id' => 'stream_1']);
        $event2 = $this->generateEvent(null, ['stream_id' => 'stream_2']);

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
                  ->method('loadStream')
                  ->willReturn([$event1, $event2]);

        $retrospector = new Retrospector(
            $this->createMock(Bus::class),
            $storeMock,
            $this->createMock(Projector::class)
        );

        $mock                 = $this->getMockForAbstractClass(Retrospection::class);
        $mock->useProjections = false;
        $mock->useReactors    = false;
        $mock->filterStreams  = [
            'exclude' => ['stream_1'],
        ];

        $mock->expects($this->once())
             ->method('preAction')
             ->with($event2)
             ->willReturn($event2);

        $mock->expects($this->once())
             ->method('preAction')
             ->with($event2);

        $retrospector->perform($mock);
    }

    public function testIncludeStream()
    {
        $event1 = $this->generateEvent(null, ['stream_id' => 'stream_1']);
        $event2 = $this->generateEvent(null, ['stream_id' => 'stream_2']);

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
                  ->method('loadStream')
                  ->willReturn([$event1, $event2]);

        $retrospector = new Retrospector(
            $this->createMock(Bus::class),
            $storeMock,
            $this->createMock(Projector::class)
        );

        $mock                 = $this->getMockForAbstractClass(Retrospection::class);
        $mock->useProjections = false;
        $mock->useReactors    = false;
        $mock->filterStreams  = [
            'include' => ['stream_1'],
        ];

        $mock->expects($this->once())
             ->method('preAction')
             ->with($event1)
             ->willReturn($event1);

        $mock->expects($this->once())
             ->method('preAction')
             ->with($event1);

        $retrospector->perform($mock);
    }

    public function testExcludeProjections()
    {
        $event1 = $this->generateEvent('EventTypeA');
        $event2 = $this->generateEvent('EventTypeB');

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
                  ->method('loadStream')
                  ->willReturn([$event1, $event2]);

        $projectorMock = $this->createMock(Projector::class);
        $projectorMock->expects($this->once())
                      ->method('projectByEvent')
                      ->with('StreamTypeA', $event2);

        $retrospector = new Retrospector(
            $this->createMock(Bus::class),
            $storeMock,
            $projectorMock
        );

        $mock                    = $this->getMockForAbstractClass(Retrospection::class);
        $mock->useProjections    = true;
        $mock->useReactors       = false;
        $mock->filterProjections = [
            'exclude' => [
                'EventTypeA',
            ],
        ];

        $mock->expects($this->exactly(2))
             ->method('preAction')
             ->willReturn($event1, $event2);

        $retrospector->perform($mock);
    }

    public function testIncludeProjections()
    {
        $event1 = $this->generateEvent('EventTypeA');
        $event2 = $this->generateEvent('EventTypeB');

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
                  ->method('loadStream')
                  ->willReturn([$event1, $event2]);

        $projectorMock = $this->createMock(Projector::class);
        $projectorMock->expects($this->once())
                      ->method('projectByEvent')
                      ->with('StreamTypeA', $event1);

        $retrospector = new Retrospector(
            $this->createMock(Bus::class),
            $storeMock,
            $projectorMock
        );

        $mock                    = $this->getMockForAbstractClass(Retrospection::class);
        $mock->useProjections    = true;
        $mock->useReactors       = false;
        $mock->filterProjections = [
            'include' => [
                'EventTypeA',
            ],
        ];

        $mock->expects($this->exactly(2))
             ->method('preAction')
             ->willReturn($event1, $event2);

        $retrospector->perform($mock);
    }


    public function testWithProjection()
    {
        $event = $this->generateEvent();

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
                  ->method('loadStream')
                  ->willReturn([$event]);

        $projectorMock = $this->createMock(Projector::class);
        $projectorMock->expects($this->once())
                      ->method('projectByEvent')
                      ->with('StreamTypeA', $event);

        $retrospector = new Retrospector(
            $this->createMock(Bus::class),
            $storeMock,
            $projectorMock
        );

        $mock              = $this->getMockForAbstractClass(Retrospection::class);
        $mock->useReactors = false;

        $mock->expects($this->once())
             ->method('preAction')
             ->with($event)
             ->willReturn($event);

        $mock->expects($this->once())
             ->method('postAction')
             ->with($event);

        $retrospector->perform($mock);
    }

    public function testWithReactor()
    {
        $event = $this->generateEvent();

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
                  ->method('loadStream')
                  ->willReturn([$event]);

        $busMock = $this->createMock(Bus::class);
        $busMock->expects($this->once())
                ->method('publish')
                ->with($event);

        $retrospector = new Retrospector(
            $busMock,
            $storeMock,
            $this->createMock(Projector::class)
        );

        $mock                 = $this->getMockForAbstractClass(Retrospection::class);
        $mock->useProjections = false;
        $mock->useReactors    = true;

        $mock->expects($this->once())
             ->method('preAction')
             ->with($event)
             ->willReturn($event);

        $mock->expects($this->once())
             ->method('postAction')
             ->with($event);

        $retrospector->perform($mock);
    }

    /**
     * @param string $mockClassName
     * @param array  $mockMeta
     *
     * @return \Framekit\Event|\PHPUnit\Framework\MockObject\MockObject
     */
    private function generateEvent($mockClassName = null, $mockMeta = [])
    {
        if(!$mockClassName) {
            $mockClassName = '';
        }
        $event           = $this->getMockForAbstractClass(Event::class, [], $mockClassName);
        $meta            = [
            'auth'        => 'uuid1',
            'ip'          => '127.0.0.1',
            'id'          => 1,
            'stream_id'   => 'stream_1',
            'stream_type' => 'StreamTypeA',
            'commited_at' => Carbon::now(),
        ];
        $meta            = array_merge($meta, $mockMeta);
        $event->__meta__ = $meta;

        return $event;
    }
}
