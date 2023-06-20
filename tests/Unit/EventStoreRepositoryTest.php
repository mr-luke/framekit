<?php

namespace Tests\Unit;

use Framekit\AggregateRoot;
use Framekit\Contracts\EventBus;
use Framekit\Contracts\Projector;
use Framekit\Contracts\Repository;
use Framekit\Contracts\Store;
use Framekit\Event;
use Framekit\Eventing\EventStoreRepository;
use Framekit\Exceptions\InvalidAggregateIdentifier;
use Illuminate\Support\Str;
use Tests\Components\DateAdded;
use Tests\Components\TestAggregate;
use Tests\UnitCase;

/**
 * EventStoreRepository unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class EventStoreRepositoryTest extends UnitCase
{
    public function testClassResolveContract()
    {
        $this->assertInstanceOf(
            Repository::class,
            new EventStoreRepository(
                $this->createMock(EventBus::class),
                $this->createMock(Store::class),
                $this->createMock(Projector::class)
            )
        );
    }

    public function testPersistMethodWithOutEvents()
    {
        $aggregateMock = $this->getMockBuilder(AggregateRoot::class)
            ->setMethods(['bootRootEntity', 'identifier', 'unpublishedEvents'])
            ->disableOriginalConstructor()
            ->getMock();

        $aggregateMock->expects($this->once())
            ->method('identifier')
            ->willReturn('test');

        $aggregateMock->expects($this->once())
            ->method('unpublishedEvents')
            ->willReturn([]);

        $busMock = $this->createMock(EventBus::class);
        $busMock->expects($this->never())
            ->method('publish');

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
            ->method('commitToStream')
            ->with(get_class($aggregateMock), $this->equalTo('test'));

        $projectorMock = $this->createMock(Projector::class);
        $projectorMock->expects($this->once())
            ->method('projectByEvents')
            ->with(
                $this->equalTo($aggregateMock),
                $this->equalTo([])
            );

        $repository = new EventStoreRepository(
            $busMock, $storeMock, $projectorMock
        );

        $repository->persist($aggregateMock);
    }

    public function testPersistMethodWithEvents()
    {
        $event = $this->createMock(Event::class);

        $aggregateMock = $this->getMockBuilder(AggregateRoot::class)
            ->setMethods(['bootRootEntity', 'identifier', 'unpublishedEvents'])
            ->disableOriginalConstructor()
            ->getMock();

        $aggregateMock->expects($this->once())
            ->method('identifier')
            ->willReturn('test');

        $aggregateMock->expects($this->once())
            ->method('unpublishedEvents')
            ->willReturn([
                $event
            ]);

        $busMock = $this->createMock(EventBus::class);
        $busMock->expects($this->once())
            ->method('publish');

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
            ->method('commitToStream')
            ->with(get_class($aggregateMock), $this->equalTo('test'));

        $projectorMock = $this->createMock(Projector::class);
        $projectorMock->expects($this->once())
            ->method('projectByEvents')
            ->with(
                $this->equalTo($aggregateMock),
                $this->equalTo([$event])
            );

        $repository = new EventStoreRepository(
            $busMock, $storeMock, $projectorMock
        );

        $repository->persist($aggregateMock);
    }

    public function testRetrieveMethod()
    {
        $uuid = Str::uuid();

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
            ->method('loadStream')
            ->with($this->equalTo($uuid))
            ->willReturn([]);

        $repository = new EventStoreRepository(
            $this->createMock(EventBus::class),
            $storeMock,
            $this->createMock(Projector::class)
        );

        $instance = $repository->retrieve(TestAggregate::class, $uuid);

        $this->assertInstanceOf(AggregateRoot::class, $instance);
    }

    public function testThrowWhenRetrieveHasNoClass()
    {
        $this->expectException(InvalidAggregateIdentifier::class);

        $repository = new EventStoreRepository(
            $this->createMock(EventBus::class),
            $this->createMock(Store::class),
            $this->createMock(Projector::class)
        );

        $repository->retrieve('test', 'test');
    }

    public function testThrowWhenRetrieveHasNoAggregate()
    {
        $this->expectException(InvalidAggregateIdentifier::class);

        $repository = new EventStoreRepository(
            $this->createMock(EventBus::class),
            $this->createMock(Store::class),
            $this->createMock(Projector::class)
        );

        $repository->retrieve(DateAdded::class, 'test');
    }
}
