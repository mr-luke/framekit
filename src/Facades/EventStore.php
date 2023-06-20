<?php

namespace Framekit\Facades;

use Framekit\Contracts\Store;
use Framekit\Testing\EventStore as Fake;
use Illuminate\Support\Facades\Facade;

/**
 * @method self assertHasEvent(string $stream_id, $event)
 * @method self assertMissingEvent(string $stream_id, $event)
 *
 * @method void commitToStream(string $stream_type, string $stream_id, array $events)
 * @method array loadStream(string $stream_id)
 *
 * @codeCoverageIgnore
 */
class EventStore extends Facade
{
    /**
     * Create faked Projector
     *
     * @return \Framekit\Contracts\Store
     */
    public static function fake(): Store
    {
        static::swap(new Fake());

        return static::getFacadeRoot();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'framekit.event.store';
    }
}
