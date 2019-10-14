<?php

declare(strict_types=1);

namespace Framekit\Testing;

use InvalidArgumentException;
use PHPUnit\Framework\Assert as PHPUnit;

use Framekit\Contracts\Serializer;
use Framekit\Contracts\Store;
use Framekit\Event;

/**
 * EventStream testing class for Framekit.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 * @version   1.0.0
 */
final class EventStore implements Store
{
    /**
     * In memory event store.
     *
     * @var array
     */
    private $events = [];

    /**
     * Determine if strema has event(s).
     *
     * @param  string $stream_id
     * @param  mixed  $event
     * @return self
     *
     * @codeCoverageIgnore
     */
    public function assertHasEvent(string $stream_id, $event): self
    {
        foreach ($this->wrap($event) as $e) {
            $name = is_string($e) ? $e : get_class($e);

            PHPUnit::assertTrue(
                $this->hasEvent($stream_id, $e),
                "Missing event [".$name."] for given stream [{$stream_id}]"
            );
        }

        return $this;
    }

    /**
     * Determine if strema has event(s).
     *
     * @param  string $stream_id
     * @param  mixed  $event
     * @return self
     *
     * @codeCoverageIgnore
     */
    public function assertMissingEvent(string $stream_id, $event): self
    {
        foreach ($this->wrap($event) as $e) {
            $name = is_string($e) ? $e : get_class($e);

            PHPUnit::assertFalse(
                $this->hasEvent($stream_id, $e),
                "Unexpected event [".$name."] in stream [{$stream_id}]"
            );
        }


        return $this;
    }

    /**
     * Wrap $event to always be array.
     *
     * @param  mixed $event
     * @return array
     *
     * @codeCoverageIgnore
     */
    private function wrap($event): array
    {
        if (is_null($event)) {
            return [];
        }

        return is_array($event) ? $event : [$event];
    }

    /**
     * Determine if given Event exists in stream & is equal.
     *
     * @param  string                  $stream_id
     * @param  \Framekit\Event|string  $event
     * @return bool
     */
    private function hasEvent(string $stream_id, $event): bool
    {
        $shallowTest = is_string($event);

        foreach ($this->loadStream($stream_id) as $e) {
            if ((!$shallowTest && $e == $event) || ($shallowTest && $e instanceof $event)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Store new payload in stream.
     *
     * @param  string $stream_type
     * @param  string $stream_id
     * @param  array  $events
     * @return void
     */
    public function commitToStream(string $stream_type, string $stream_id, array $events): void
    {
        if (!isset($this->events[$stream_id])) {
            $this->events[$stream_id] = [];
        }

        array_push($this->events[$stream_id], ...$events);
    }

    /**
     * Load Stream based on id.
     *
     * @param  string $stream_id
     * @return array
     *
     * @codeCoverageIgnore
     */
    public function loadStream(string $stream_id = null): array
    {
        return $this->events[$stream_id] ?? [];
    }
}
