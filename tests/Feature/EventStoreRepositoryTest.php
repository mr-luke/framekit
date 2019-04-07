<?php

namespace Tests\Feature;

use Tests\FeatureCase;

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
class EventStoreRepositoryTest extends FeatureCase
{
    public function testRetriveMethod()
    {
        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
                  ->method('loadStream')
                  ->with($this->equalTo('test'))
                  ->willReturn([]);

        $repository = new EventStoreRepository(
            $this->createMock(Bus::class),
            $storeMock,
            $this->createMock(Projector::class)
        );

        $instance = $repository->retrieve(\Tests\Components\TestAggregate::class, 'test');

        $this->assertInstanceOf(AggregateRoot::class, $instance);
    }
}
