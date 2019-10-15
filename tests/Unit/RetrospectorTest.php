<?php

namespace Tests\Unit;

use Tests\UnitCase;

use Framekit\Contracts\Bus;
use Framekit\Contracts\Projector;
use Framekit\Contracts\Retrospector as Contract;
use Framekit\Contracts\Store;
use Framekit\Eventing\Retrospector;
use Framekit\Event;
use Framekit\Retrospection;

/**
 * Retrospector unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class RetrospectorTest extends UnitCase
{
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
                    ['stream_type' => 'StreamB', 'stream_id' => 'stream_2']
                ]);

        $retrospector = new Retrospector(
            $this->createMock(Bus::class),
            $storeMock,
            $this->createMock(Projector::class)
        );

        $mock = $this->getMockForAbstractClass(Retrospection::class);
        $mock->filterStreams = ['include' => [], 'exclude' => []];

        $retrospector->perform($mock);
    }

    public function testExcludeStream()
    {
        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
                ->method('getAvailableStreams')
                ->willReturn([
                    ['stream_type' => 'StreamA', 'stream_id' => 'stream_1'],
                    ['stream_type' => 'StreamB', 'stream_id' => 'stream_2']
                ]);

        $storeMock->expects($this->once())
                ->method('loadStream')
                ->with('stream_2')
                ->willReturn([]);

        $retrospector = new Retrospector(
            $this->createMock(Bus::class),
            $storeMock,
            $this->createMock(Projector::class)
        );

        $mock = $this->getMockForAbstractClass(Retrospection::class);
        $mock->filterStreams = ['exclude' => ['stream_1']];
        $mock->useProjections = false;
        $mock->useReactors = false;

        $retrospector->perform($mock);
    }

    public function testIncludeStream()
    {
        $event = $this->getMockForAbstractClass(Event::class);

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
                ->method('getAvailableStreams')
                ->willReturn([
                    ['stream_type' => 'StreamA', 'stream_id' => 'stream_1'],
                    ['stream_type' => 'StreamB', 'stream_id' => 'stream_2']
                ]);

        $storeMock->expects($this->once())
                ->method('loadStream')
                ->with('stream_1')
                ->willReturn([$event]);

        $retrospector = new Retrospector(
            $this->createMock(Bus::class),
            $storeMock,
            $this->createMock(Projector::class)
        );

        $mock = $this->getMockForAbstractClass(Retrospection::class);
        $mock->filterStreams = ['include' => ['stream_1']];
        $mock->useProjections = false;
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

    public function testWithProjection()
    {
        $event = $this->getMockForAbstractClass(Event::class);

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
                ->method('getAvailableStreams')
                ->willReturn([
                    ['stream_type' => 'StreamA', 'stream_id' => 'stream_1']
                ]);

        $storeMock->expects($this->once())
                ->method('loadStream')
                ->with('stream_1')
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

        $mock = $this->getMockForAbstractClass(Retrospection::class);
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
                ->method('getAvailableStreams')
                ->willReturn([
                    ['stream_type' => 'StreamA', 'stream_id' => 'stream_1']
                ]);

        $storeMock->expects($this->once())
                ->method('loadStream')
                ->with('stream_1')
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

        $mock = $this->getMockForAbstractClass(Retrospection::class);
        $mock->useProjections = false;

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
