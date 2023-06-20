<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Framekit\Testing\EventStore;
use Tests\Components\IntegerAdded;
use Tests\NonPublicMethodTool;
use Tests\UnitCase;

/**
 * EventBus unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class TestingEventStoreTest extends UnitCase
{
    use NonPublicMethodTool;

    public function testCommitingToStream()
    {
        $event = new IntegerAdded('uuid1', 2);

        $store = new EventStore();
        $store->commitToStream('Stream','uuid1', [$event]);

        $this->assertEquals(
            $store->loadStream('uuid1'),
            [$event]
        );
    }

    public function testWhenStreamHasNotTheSameEvent()
    {
        $event = new IntegerAdded('uuid', 2);

        $store = new EventStore();
        $store->commitToStream('Stream','uuid1', [$event]);

        $compose = self::getMethodOfClass(EventStore::class, 'hasEvent');

        $this->assertFalse(
            $compose->invokeArgs($store, [
                'uuid1',
                new IntegerAdded('uuid', 3)
            ])
        );
    }

    public function testWhenStreamHasEventOfGivenType()
    {
        $event = new IntegerAdded('uuid', 2);

        $store = new EventStore();
        $store->commitToStream('Stream','uuid1', [$event]);

        $compose = self::getMethodOfClass(EventStore::class, 'hasEvent');

        $this->assertTrue(
            $compose->invokeArgs($store, [
                'uuid1',
                IntegerAdded::class
            ])
        );
    }

    public function testWhenStreamHasTheSameEvent()
    {
        $event = new IntegerAdded('uuid', 2);

        $store = new EventStore();
        $store->commitToStream('Stream','uuid1', [$event]);

        $compose = self::getMethodOfClass(EventStore::class, 'hasEvent');

        $this->assertTrue(
            $compose->invokeArgs($store, [
                'uuid1',
                $event
            ])
        );
    }
}
