<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Framekit\Contracts\Config;
use Framekit\Contracts\Mapper;
use Framekit\Contracts\Serializer;
use Framekit\Contracts\Store;
use Framekit\Drivers\EventStore;
use Framekit\Eventing\EventSerializer;
use Framekit\Exceptions\MethodUnknown;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Mrluke\Configuration\Contracts\ArrayHost;
use Tests\AppCase;
use Tests\Components\DummyReactor;
use Tests\Components\IntegerAdded;
use Tests\NonPublicMethodTool;

/**
 * EventStore feature tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
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
            'class'   => IntegerAdded::class,
            'payload' => [],
        ]);

        $compose = self::getMethodOfClass(EventStore::class, 'isVersionConflict');

        $this->assertTrue(
            !$compose->invokeArgs($eventStore, [
                $payload, 1,
            ])
        );

        $this->assertTrue(
            $compose->invokeArgs($eventStore, [
                $payload, 0,
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

        $config     = $this->app->make(Config::class);
        $mapper     = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            $this->createMock(Serializer::class),
            $mapper
        );

        $eventStore->commitToStream('Stream', 'stream_1', [
            new DummyReactor,
        ]);
    }

    public function testCommiting()
    {
        $config     = $this->app->make(Config::class);
        $mapper     = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            new EventSerializer,
            $mapper
        );

        $eventStore->commitToStream('Stream', 'stream_1', [
            new IntegerAdded(2),
        ]);

        $this->assertDatabaseHas($config->get('tables.eventstore'), [
            'stream_type' => 'Stream',
            'stream_id'   => 'stream_1',
            'event'       => IntegerAdded::class,
        ]);
    }

    public function testLoadStream()
    {
        $config     = $this->app->make(Config::class);
        $mapper     = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            new EventSerializer,
            $mapper
        );

        DB::table($config->get('tables.eventstore'))->insert([
            'stream_type' => 'Stream',
            'stream_id'   => 'stream_1',
            'event'       => IntegerAdded::class,
            'payload'     => json_encode([
                'class'      => IntegerAdded::class,
                'attributes' => [
                    'toAdd' => 2,
                ],
            ]),
            'version'     => 1,
            'meta'        => '{"auth":null,"ip":"127.0.0.1"}',
            'commited_at' => now(),
        ]);

        $events = $eventStore->loadStream('stream_1');

        $this->assertTrue(is_array($events));
        $this->assertInstanceOf(
            IntegerAdded::class,
            $events[0]
        );
    }

    public function testLoadStreamWithMeta()
    {
        $config     = $this->app->make(Config::class);
        $mapper     = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            new EventSerializer,
            $mapper
        );

        DB::table($config->get('tables.eventstore'))->insert([
            'stream_type' => 'Stream',
            'stream_id'   => 'stream_1',
            'event'       => IntegerAdded::class,
            'payload'     => json_encode([
                'class'      => IntegerAdded::class,
                'attributes' => [
                    'toAdd' => 2,
                ],
            ]),
            'version'     => 1,
            'meta'        => '{"auth":null,"ip":"127.0.0.1"}',
            'commited_at' => now(),
        ]);

        $events = $eventStore->loadStream('stream_1', null, null, true);

        $this->assertTrue(is_array($events));
        $this->assertInstanceOf(
            IntegerAdded::class,
            $events[0]
        );

        $this->assertNotEmpty($events[0]->__meta__);

    }

    public function testLoadStreamEventsSince()
    {
        $config     = $this->app->make(Config::class);
        $mapper     = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            new EventSerializer,
            $mapper
        );

        $this->insertEvents($config);

        $events = $eventStore->loadStream(null, '2019-10-05 22:00:00', null, true);
        $this->assertCount(5, $events);
        $this->assertEquals('2019-10-06 10:25:00.000000', $events[0]->__meta__['commited_at']);
    }

    public function testLoadStreamEventsTill()
    {
        $config     = $this->app->make(Config::class);
        $mapper     = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            new EventSerializer,
            $mapper
        );

        $this->insertEvents($config);

        $events = $eventStore->loadStream(null, null, '2019-10-05 00:00:00', true);
        $this->assertCount(4, $events);
        $this->assertEquals('2019-10-04 10:25:00.000000', $events[3]->__meta__['commited_at']);
    }

    public function testLoadStreamEventsSinceAndTill()
    {
        $config     = $this->app->make(Config::class);
        $mapper     = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            new EventSerializer,
            $mapper
        );

        $this->insertEvents($config);

        $events = $eventStore->loadStream(null, '2019-10-05 00:00:00', '2019-10-06 23:59:59', true);
        $this->assertCount(2, $events);
        $this->assertEquals('2019-10-05 10:25:00.000000', $events[0]->__meta__['commited_at']);
    }

    public function testLoadStreamEventsSinceAndTillForSpecyficStream()
    {
        $config     = $this->app->make(Config::class);
        $mapper     = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            new EventSerializer,
            $mapper
        );

        $this->insertEvents($config, 'stream_1');
        $this->insertEvents($config ,'stream_2');

        $events = $eventStore->loadStream('stream_2', '2019-10-05 00:00:00', '2019-10-06 23:59:59', true);
        $this->assertCount(2, $events);
        $this->assertEquals('2019-10-05 10:25:00.000000', $events[0]->__meta__['commited_at']);
        $this->assertEquals('stream_2', $events[0]->__meta__['stream_id']);
    }

    public function testLoadStreamWithConflict()
    {
        $config     = $this->app->make(Config::class);
        $mapper     = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            new EventSerializer,
            $mapper
        );

        DB::table($config->get('tables.eventstore'))->insert([
            'stream_type' => 'Stream',
            'stream_id'   => 'stream_1',
            'event'       => IntegerAdded::class,
            'payload'     => json_encode([
                'class'      => IntegerAdded::class,
                'attributes' => [
                    'toAdd' => 2,
                ],
            ]),
            'version'     => 2,
            'meta'        => '{"auth":null,"ip":"127.0.0.1"}',
            'commited_at' => now(),
        ]);

        $events = $eventStore->loadStream('stream_1');

        $this->assertInstanceOf(
            IntegerAdded::class,
            $events[0]
        );
    }

    public function testAvailableStreamList()
    {
        $config     = $this->app->make(Config::class);
        $mapper     = $this->app->make(Mapper::class);
        $eventStore = new EventStore(
            $config,
            new EventSerializer,
            $mapper
        );

        DB::table($config->get('tables.eventstore'))->insert([
            [
                'stream_type' => 'StreamA',
                'stream_id'   => 'stream_1',
                'event'       => IntegerAdded::class,
                'payload'     => '[]',
                'version'     => 1,
                'meta'        => '[]',
                'commited_at' => now(),
            ],
            [
                'stream_type' => 'StreamB',
                'stream_id'   => 'stream_2',
                'event'       => IntegerAdded::class,
                'payload'     => '[]',
                'version'     => 1,
                'meta'        => '[]',
                'commited_at' => now(),
            ],
            [
                'stream_type' => 'StreamA',
                'stream_id'   => 'stream_1',
                'event'       => IntegerAdded::class,
                'payload'     => '[]',
                'version'     => 1,
                'meta'        => '[]',
                'commited_at' => now(),
            ],
            [
                'stream_type' => 'StreamC',
                'stream_id'   => 'stream_3',
                'event'       => IntegerAdded::class,
                'payload'     => '[]',
                'version'     => 1,
                'meta'        => '[]',
                'commited_at' => now(),
            ],
        ]);

        $streams = $eventStore->getAvailableStreams();

        $this->assertEquals(
            [
                ['stream_type' => 'StreamA', 'stream_id' => 'stream_1'],
                ['stream_type' => 'StreamB', 'stream_id' => 'stream_2'],
                ['stream_type' => 'StreamC', 'stream_id' => 'stream_3'],
            ],
            $streams
        );
    }

    public function testThrowsWhenCallingAssertForProd()
    {
        $this->expectException(MethodUnknown::class);

        $eventStore = new EventStore(
            $this->app->make(Config::class),
            new EventSerializer,
            $this->app->make(Mapper::class)
        );
        $eventStore->assertHasEvent();
    }

    /**
     * @param        $config
     * @param string $streamId
     */
    private function insertEvents($config, $streamId = 'stream_1')
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table($config->get('tables.eventstore'))->insert([
                'stream_type' => 'Stream',
                'stream_id'   => $streamId,
                'event'       => IntegerAdded::class,
                'payload'     => json_encode([
                    'class'      => IntegerAdded::class,
                    'attributes' => [
                        'toAdd' => 2,
                    ],
                ]),
                'version'     => 1,
                'meta'        => '{"auth":null,"ip":"127.0.0.1"}',
                'commited_at' => Carbon::parse(sprintf('2019-10-%d 10:25:00', $i)),
            ]);
        }
    }
}
