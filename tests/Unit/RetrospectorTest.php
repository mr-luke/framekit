<?php

namespace Tests\Unit;

use Framekit\Contracts\Bus;
use Framekit\Contracts\Projector;
use Framekit\Contracts\Retrospector as Contract;
use Framekit\Contracts\Store;
use Framekit\Event;
use Framekit\Eventing\Retrospector;
use Framekit\Retrospection;
use Tests\NonPublicMethodTool;
use Tests\UnitCase;

/**
 * Retrospector unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
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
        $this->expectException(\InvalidArgumentException::class);

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
                  ->method('getAvailableStreams')
                  ->willReturn([
                      ['stream_type' => 'StreamA', 'stream_id' => 'stream_1'],
                      ['stream_type' => 'StreamB', 'stream_id' => 'stream_2'],
                  ]);

        $retrospector = new Retrospector(
            $this->createMock(Bus::class),
            $storeMock,
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
        //
    }

    public function testIncludeStream()
    {
        //
    }

    public function testFilterEventsSince()
    {
        //
    }

    public function testFilterEventsTill()
    {
        //
    }

    public function testFilterEventsSinceAndTill()
    {
        //
    }

    public function testFilterEventsSinceAndTillForSpecyficStream()
    {
        //
    }

    public function testIncludeProjections()
    {
        //
    }

    public function testExcludeProjections()
    {
        //
    }

    public function testWithProjection()
    {
        $event = $this->getMockForAbstractClass(Event::class);

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
                  ->method('loadStream')
                  ->willReturn([$event]);

        $projectorMock = $this->createMock(Projector::class);
        $projectorMock->expects($this->once())
                      ->method('projectByEvent')
                      ->with('StreamA', $event);

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
             ->method('preAction')
             ->with($event);

        $retrospector->perform($mock);
    }

    public function testWithReactor()
    {
        $event = $this->getMockForAbstractClass(Event::class);

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
        $mock->useReactors    = false;

        $mock->expects($this->once())
             ->method('preAction')
             ->with($event)
             ->willReturn($event);

        $mock->expects($this->once())
             ->method('preAction')
             ->with($event);

        $retrospector->perform($mock);
    }
}
