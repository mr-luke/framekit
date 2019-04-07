<?php

namespace Tests\Unit;

use Tests\UnitCase;

use Framekit\AggregateRoot;
use Framekit\Contracts\Bus;
use Framekit\Contracts\Projector;
use Framekit\Contracts\Repository;
use Framekit\Contracts\Store;
use Framekit\Eventing\EventStoreRepository;
use Framekit\Event;
use Framekit\Exceptions\UnsupportedEvent;

/**
 * EventStoreRepository unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class EventStoreRepositoryTest extends UnitCase
{
    public function testClassResolveContract()
    {
        $this->assertInstanceOf(
            Repository::class,
            new EventStoreRepository(
                $this->createMock(Bus::class),
                $this->createMock(Store::class),
                $this->createMock(Projector::class)
            )
        );
    }

    public function testPersistMethodWithOutEvents()
    {
        $aggreagateMock = $this->getMockBuilder(AggregateRoot::class)
                               ->setMethods(['boot', 'getId', 'getUncommitedEvents'])
                               ->disableOriginalConstructor()
                               ->getMock();

        $aggreagateMock->expects($this->once())
                       ->method('getId')
                       ->willReturn('test');

        $aggreagateMock->expects($this->once())
                       ->method('getUncommitedEvents')
                       ->willReturn([]);

        $busMock = $this->createMock(Bus::class);
        $busMock->expects($this->never())
                ->method('publish');

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
                  ->method('commitToStream')
                  ->with($this->equalTo('test'));

        $projectorMock = $this->createMock(Projector::class);
        $projectorMock->expects($this->once())
                      ->method('project')
                      ->with(
                          $this->equalTo($aggreagateMock),
                          $this->equalTo([])
                      );

        $repository = new EventStoreRepository(
            $busMock, $storeMock, $projectorMock
        );

        $repository->persist($aggreagateMock);
    }

    public function testPersistMethodWithEvents()
    {
        $event = $this->createMock(Event::class);

        $aggreagateMock = $this->getMockBuilder(AggregateRoot::class)
                               ->setMethods(['boot', 'getId', 'getUncommitedEvents'])
                               ->disableOriginalConstructor()
                               ->getMock();

        $aggreagateMock->expects($this->once())
                       ->method('getId')
                       ->willReturn('test');

        $aggreagateMock->expects($this->once())
                       ->method('getUncommitedEvents')
                       ->willReturn([
                           $event
                       ]);

        $busMock = $this->createMock(Bus::class);
        $busMock->expects($this->once())
                ->method('publish');

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
                  ->method('commitToStream')
                  ->with($this->equalTo('test'));

        $projectorMock = $this->createMock(Projector::class);
        $projectorMock->expects($this->once())
                      ->method('project')
                      ->with(
                          $this->equalTo($aggreagateMock),
                          $this->equalTo([$event])
                      );

        $repository = new EventStoreRepository(
            $busMock, $storeMock, $projectorMock
        );

        $repository->persist($aggreagateMock);
    }

    public function testTrownWhenRetriveHasNoClass()
    {
        $this->expectException(UnsupportedEvent::class);

        $repository = new EventStoreRepository(
            $this->createMock(Bus::class),
            $this->createMock(Store::class),
            $this->createMock(Projector::class)
        );

        $repository->retrieve('test', 'test');
    }

    public function testTrownWhenRetriveHasNoAggregate()
    {
        $this->expectException(UnsupportedEvent::class);

        $repository = new EventStoreRepository(
            $this->createMock(Bus::class),
            $this->createMock(Store::class),
            $this->createMock(Projector::class)
        );

        $repository->retrieve(\Tests\Components\DateAdded::class, 'test');
    }
}
