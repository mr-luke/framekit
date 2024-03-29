<?php

use Framekit\Drivers\EventBus;
use Framekit\Drivers\EventStore;
use Framekit\Drivers\Projector;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Drivers
    |--------------------------------------------------------------------------
    |
    | Framekit allows to use different drivers for Stream & Snapshots support.
    | By default it uses Database drivers based on Illuminate\Database.
    |
    */

    'drivers' => [
        'event_bus'   => EventBus::class,
        'event_store' => EventStore::class,
        'projector'   => Projector::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Queue connections
    |--------------------------------------------------------------------------
    |
    | Framekit allows to use async Bus. Here you can set queue connection for
    | each of Framekit's bus.
    |
    */

    'queues' => [
        'event_bus' => env('EVENTBUS_QUEUE'),
        'projector' => env('PROJECTOR_QUEUE')
    ],

    /*
    |--------------------------------------------------------------------------
    | Snapshot interval
    |--------------------------------------------------------------------------
    |
    | Framekit allows you to determine when new snapshot should be created.
    | By default each 100 events new snapshot will occur. You can disable this
    | feature by settings 'null' value.
    |
    | Allowed: int | null
    |
    */

    'snapshot_after' => env('EVENTSTORE_SNAPSHOTS', 100),

    /*
    |--------------------------------------------------------------------------
    | Event Store tables
    |--------------------------------------------------------------------------
    |
    | These options configure tables name for Event Store. You are free to set
    | them as you want. Remember that these are most important tables is your
    | future system. You can use different database to store them.
    |
    */

    'database' => env('EVENTSTORE_CONNECTION', env('DB_CONNECTION')),

    'tables' => [
        'eventstore' => 'eventstore',
        'snapshots'  => 'eventstore_snapshots',
    ],

    /*
    |--------------------------------------------------------------------------
    | Skip events
    |--------------------------------------------------------------------------
    |
    | Decide if recreating from stream needs to apply event changes.
    | If the value is true, the applying method must exist in the aggregate,
    | otherwise an exception will be thrown.
    | If the value is false, the method does not have to exist on the aggregate
    | and when trying to recreate from a non-existing method it will be skipped.
    |
    */

    'skip_events' => env('FRAMEKIT_SKIP_EVENTS', true)
];
