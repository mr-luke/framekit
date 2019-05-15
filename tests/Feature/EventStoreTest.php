<?php

namespace Tests\Feature;

use Tests\AppCase;
use Tests\NonPublicMethodTool;

use Framekit\Contracts\Config;
use Framekit\Contracts\Serializer;
use Framekit\Contracts\Store;
use Framekit\Drivers\EventStore;
use Framekit\Exceptions\MethodUnknown;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Mrluke\Configuration\Contracts\ArrayHost;

/**
 * EventStore feature tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class EventStoreTest extends AppCase
{
    use NonPublicMethodTool;

    public function testClassResolveContract()
    {
        $eventStore = new EventStore(
            $this->createMock(ArrayHost::class),
            $this->createMock(Serializer::class)
        );

        $this->assertInstanceOf(
            Store::class,
            $eventStore
        );
    }

    public function testCanDetectVersionConflict()
    {
        $eventStore = new EventStore(
            $this->createMock(ArrayHost::class),
            $this->createMock(Serializer::class)
        );

        $payload = json_encode([
            'class' => \Tests\Components\IntegerAdded::class,
            'payload' => []
        ]);

        $compose = self::getMethodOfClass(EventStore::class, 'isVersionConflict');

        $this->assertTrue(
            !$compose->invokeArgs($eventStore, [
                $payload, 1
            ])
        );

        $this->assertTrue(
            $compose->invokeArgs($eventStore, [
                $payload, 0
            ])
        );
    }

    public function testVersionMapping()
    {
        $eventStore = new EventStore(
            $this->createMock(ArrayHost::class),
            $this->createMock(Serializer::class)
        );

        $payload = 'test';
        $compose = self::getMethodOfClass(EventStore::class, 'mapVersion');

        $this->assertEquals(
            $payload,
            $compose->invokeArgs($eventStore, [$payload])
        );
    }

    public function testComposerCommon()
    {
        $eventStore = new EventStore(
            $this->createMock(ArrayHost::class),
            $this->createMock(Serializer::class)
        );

        $compose = self::getMethodOfClass(EventStore::class, 'composeCommon');
        $array   = $compose->invokeArgs($eventStore, ['stream1']);

        $this->assertEquals(
            'stream1',
            $array['stream_id']
        );
    }

    public function testThrowWhenCommitingNonEvent()
    {
        $this->expectException(InvalidArgumentException::class);

        $config = $this->app->make(Config::class);
        $eventStore = new EventStore(
            $config,
            $this->createMock(Serializer::class)
        );

        $eventStore->commitToStream('stream_1', [
            new \Tests\Components\DummyReactor
        ]);
    }

    public function testCommiting()
    {
        $config = $this->app->make(Config::class);
        $eventStore = new EventStore(
            $config,
            new \Framekit\Eventing\EventSerializer
        );

        $eventStore->commitToStream('stream_1', [
            new \Tests\Components\IntegerAdded(2)
        ]);

        $this->assertDatabaseHas($config->get('tables.eventstore'), [
            'stream_id' => 'stream_1',
            'event'     => \Tests\Components\IntegerAdded::class
        ]);
    }

    public function testLoadStrem()
    {
        $config = $this->app->make(Config::class);
        $eventStore = new EventStore(
            $config,
            new \Framekit\Eventing\EventSerializer
        );

        DB::table($config->get('tables.eventstore'))->insert([
            'stream_id' => 'stream_1',
            'event'     => \Tests\Components\IntegerAdded::class,
            'payload'   => json_encode([
                'class'      => \Tests\Components\IntegerAdded::class,
                'attributes' => [
                    'toAdd' => 2
                ]
            ]),
            'version'   => 1,
            'meta'      => '{"auth":null,"ip":"127.0.0.1"}',
            'commited_at' => now()
        ]);

        $events = $eventStore->loadStream('stream_1');

        $this->assertTrue(is_array($events));
        $this->assertInstanceOf(
            \Tests\Components\IntegerAdded::class,
            $events[0]
        );
    }

    public function testLoadStreamWithConflict()
    {
        $config = $this->app->make(Config::class);
        $eventStore = new EventStore(
            $config,
            new \Framekit\Eventing\EventSerializer
        );

        DB::table($config->get('tables.eventstore'))->insert([
            'stream_id' => 'stream_1',
            'event'     => \Tests\Components\IntegerAdded::class,
            'payload'   => json_encode([
                'class'      => \Tests\Components\IntegerAdded::class,
                'attributes' => [
                    'toAdd' => 2
                ]
            ]),
            'version'   => 2,
            'meta'      => '{"auth":null,"ip":"127.0.0.1"}',
            'commited_at' => now()
        ]);

        $events = $eventStore->loadStream('stream_1');

        $this->assertInstanceOf(
            \Tests\Components\IntegerAdded::class,
            $events[0]
        );
    }

    public function testThrowsWhenCallingAssertForProd()
    {
        $this->expectException(MethodUnknown::class);

        $eventStore = new EventStore(
            $this->app->make(Config::class),
            new \Framekit\Eventing\EventSerializer
        );
        $eventStore->assertHasEvent();
    }
}
