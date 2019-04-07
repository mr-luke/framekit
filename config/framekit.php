<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Drivers
    |--------------------------------------------------------------------------
    |
    | Framekit allows to use different drivers for Steram & Snapshots support.
    | By default it uses Database drivers based on Illuminate\Database.
    |
    */

    'drivers' => [
        'command_bus' => \Framekit\Drivers\CommandBus::class,
        'event_bus'   => \Framekit\Drivers\EventBus::class,
        'event_store' => \Framekit\Drivers\EventStore::class,
        'projector'   => \Framekit\Drivers\Projector::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Snapshot interval
    |--------------------------------------------------------------------------
    |
    | Framekit allows you to determine when new snapshop should be created.
    | By default each 100 events new snapshot will occure. You can disable this
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
    ]
];
