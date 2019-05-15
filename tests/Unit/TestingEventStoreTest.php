<?php

namespace Tests\Unit;

use Tests\Components\IntegerAdded;
use Tests\NonPublicMethodTool;
use Tests\UnitCase;

use Framekit\Testing\EventStore;

/**
 * EventBus unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class TestingEventStoreTest extends UnitCase
{
    use NonPublicMethodTool;

    public function testCommitingToStream()
    {
        $event = new IntegerAdded(2);

        $store = new EventStore();
        $store->commitToStream('uuid1', [$event]);

        $this->assertEquals(
            $store->loadStream('uuid1'),
            [$event]
        );
    }

    public function testWhenStreamHasNotTheSameEvent()
    {
        $event = new IntegerAdded(2);

        $store = new EventStore();
        $store->commitToStream('uuid1', [$event]);

        $compose = self::getMethodOfClass(EventStore::class, 'hasEvent');

        $this->assertFalse(
            $compose->invokeArgs($store, [
                'uuid1',
                new IntegerAdded(3)
            ])
        );
    }

    public function testWhenStreamHasTheSameEvent()
    {
        $event = new IntegerAdded(2);

        $store = new EventStore();
        $store->commitToStream('uuid1', [$event]);

        $compose = self::getMethodOfClass(EventStore::class, 'hasEvent');

        $this->assertTrue(
            $compose->invokeArgs($store, [
                'uuid1',
                $event
            ])
        );
    }
}
