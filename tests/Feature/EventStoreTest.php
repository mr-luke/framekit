<?php

namespace Tests\Feature;

use Tests\AppCase;
use Tests\NonPublicMethodTool;

use Framekit\Contracts\Config;
use Framekit\Contracts\Mapper;
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
            $this->createMock(Serializer::class),
            $this->createMock(Mapper::class)
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
            $this->createMock(Serializer::class),
            $this->createMock(Mapper::class)
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
            $this->createMock(Serializer::class),
            $this->app->make(Mapper::class)
        );

        $payload = '{"test":"test2"}';
        $compose = self::getMethodOfClass(EventStore::class, 'mapVersion');

        $this->assertEquals(
            $payload,
            $compose->invokeArgs($eventStore, [$payload, 1, []])
        );
    }

    public function testComposerCommon()
    {
        $eventStore = new EventStore(
            $this->createMock(ArrayHost::class),
            $this->createMock(Serializer::class),
            $this->createMock(Mapper::class)
        );

        $compose = self::getMethodOfClass(EventStore::class, 'composeCommon');
        $array   = $compose->invokeArgs($eventStore, ['Stream', 'stream1']);

        $this->assertEquals(
            'stream1',
            $array['stream_id']
        );
    }

    public function testThrowWhenCommitingNonEvent()
    {
        $this->expectException(InvalidArgumentException::class);

        $config = $this->app->make(Config::class);
        $mapper = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            $this->createMock(Serializer::class),
            $mapper
        );

        $eventStore->commitToStream('Stream', 'stream_1', [
            new \Tests\Components\DummyReactor
        ]);
    }

    public function testCommiting()
    {
        $config = $this->app->make(Config::class);
        $mapper = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            new \Framekit\Eventing\EventSerializer,
            $mapper
        );

        $eventStore->commitToStream('Stream', 'stream_1', [
            new \Tests\Components\IntegerAdded(2)
        ]);

        $this->assertDatabaseHas($config->get('tables.eventstore'), [
            'stream_type' => 'Stream',
            'stream_id'   => 'stream_1',
            'event'       => \Tests\Components\IntegerAdded::class
        ]);
    }

    public function testLoadStrem()
    {
        $config = $this->app->make(Config::class);
        $mapper = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            new \Framekit\Eventing\EventSerializer,
            $mapper
        );

        DB::table($config->get('tables.eventstore'))->insert([
            'stream_type' => 'Stream',
            'stream_id'   => 'stream_1',
            'event'       => \Tests\Components\IntegerAdded::class,
            'payload'     => json_encode([
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
        $mapper = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            new \Framekit\Eventing\EventSerializer,
            $mapper
        );

        DB::table($config->get('tables.eventstore'))->insert([
            'stream_type' => 'Stream',
            'stream_id'   => 'stream_1',
            'event'       => \Tests\Components\IntegerAdded::class,
            'payload'     => json_encode([
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

    public function testAvailableStreamList()
    {
        $config = $this->app->make(Config::class);
        $mapper = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            new \Framekit\Eventing\EventSerializer,
            $mapper
        );

        DB::table($config->get('tables.eventstore'))->insert([
            [
                'stream_type' => 'StreamA',
                'stream_id'   => 'stream_1',
                'event'       => \Tests\Components\IntegerAdded::class,
                'payload'     => '',
                'version'     => 1,
                'meta'        => '',
                'commited_at' => now()
            ],
            [
                'stream_type' => 'StreamB',
                'stream_id'   => 'stream_2',
                'event'       => \Tests\Components\IntegerAdded::class,
                'payload'     => '',
                'version'     => 1,
                'meta'        => '',
                'commited_at' => now()
            ],
            [
                'stream_type' => 'StreamA',
                'stream_id'   => 'stream_1',
                'event'       => \Tests\Components\IntegerAdded::class,
                'payload'     => '',
                'version'     => 1,
                'meta'        => '',
                'commited_at' => now()
            ],
            [
                'stream_type' => 'StreamC',
                'stream_id'   => 'stream_3',
                'event'       => \Tests\Components\IntegerAdded::class,
                'payload'     => '',
                'version'     => 1,
                'meta'        => '',
                'commited_at' => now()
            ]
        ]);

        $streams = $eventStore->getAvailableStreams();

        $this->assertEquals(
            [
                ['stream_type' => 'StreamA', 'stream_id' => 'stream_1'],
                ['stream_type' => 'StreamB', 'stream_id' => 'stream_2'],
                ['stream_type' => 'StreamC', 'stream_id' => 'stream_3']
            ],
            $streams
        );
    }

    public function testThrowsWhenCallingAssertForProd()
    {
        $this->expectException(MethodUnknown::class);

        $eventStore = new EventStore(
            $this->app->make(Config::class),
            new \Framekit\Eventing\EventSerializer,
            $this->app->make(Mapper::class)
        );
        $eventStore->assertHasEvent();
    }
}
