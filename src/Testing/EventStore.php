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
     * @param  string $event
     * @return self
     *
     * @codeCoverageIgnore
     */
    public function assertHasEvent(string $stream_id, $event): self
    {
        $events = $this->wrap($event);

        foreach ($events as $e) {
            PHPUnit::assertTrue(
                $this->hasEvent($stream_id, $e),
                "Missing event [".get_class($e)."] for given stream [{$stream_id}]"
            );
        }

        return $this;
    }

    /**
     * Determine if strema has event(s).
     *
     * @param  string $stream_id
     * @param  string $event
     * @return self
     *
     * @codeCoverageIgnore
     */
    public function assertMissingEvent(string $stream_id, $event): self
    {
        $events = $this->wrap($event);

        foreach ($events as $e) {
            PHPUnit::assertFalse(
                $this->hasEvent($stream_id, $e),
                "Unexpected event [".get_class($e)."] in stream [{$stream_id}]"
            );
        }


        return $this;
    }

    /**
     * Determine if given Event exists in stream & is equal.
     *
     * @param  string           $stream_id
     * @param  \Framekit\Event  $event
     * @return bool
     */
    private function hasEvent(string $stream_id, Event $event): bool
    {
        foreach ($this->loadStream($stream_id) as $e) {
            if ($e == $event) {
                return true;
            }
        }

        return false;
    }

    /**
     * Store new payload in stream.
     *
     * @param  string $stream_id
     * @param  array  $events
     * @return void
     */
    public function commitToStream(string $stream_id, array $events): void
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
    public function loadStream(string $stream_id): array
    {
        return $this->events[$stream_id] ?? [];
    }
}
