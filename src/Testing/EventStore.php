<?php

declare(strict_types=1);

namespace Framekit\Testing;

use Framekit\Contracts\Store;
use Framekit\Event;
use PHPUnit\Framework\Assert as PHPUnit;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
final class EventStore implements Store
{
    /**
     * In memory event store.
     *
     * @var array
     */
    private array $events = [];

    /**
     * Determine if stream has event(s).
     *
     * @param string $stream_id
     * @param mixed  $event
     *
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
                "Missing event [" . $name . "] for given stream [{$stream_id}]"
            );
        }

        return $this;
    }

    /**
     * Determine if stream has event(s).
     *
     * @param string $stream_id
     * @param mixed  $event
     *
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
                "Unexpected event [" . $name . "] in stream [{$stream_id}]"
            );
        }


        return $this;
    }

    /**
     * @inheritDoc
     */
    public function commitToStream(string $streamType, string $streamId, array $events): void
    {
        if (!isset($this->events[$streamId])) {
            $this->events[$streamId] = [];
        }

        array_push($this->events[$streamId], ...$events);
    }

    /**
     * @inheritDoc
     */
    public function getAvailableStreams(): array
    {
        return array_keys($this->events);
    }

    /**
     * @inheritDoc
     */
    public function loadStream(
        string  $streamId = null,
        ?string $since = null,
        ?string $till = null,
        bool    $withMeta = false
    ): array {
        return $this->events[$streamId] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function overrideEvent(
        int    $eventId,
        string $event = null,
        array  $payload = null,
        int    $seqNo = null
    ): void {
        // DO nothing
    }

    /**
     * Make a deep test of event.
     *
     * @param \Framekit\Event $toTest
     * @param \Framekit\Event $fromStream
     * @return bool
     */
    private function eventDeepTest(Event $toTest, Event $fromStream): bool
    {
        return $toTest == $fromStream;
    }

    /**
     * Make a shallow test of event.
     *
     * @param string          $toTest
     * @param \Framekit\Event $fromStream
     * @return bool
     */
    private function eventShallowTest(string $toTest, Event $fromStream): bool
    {
        return $fromStream instanceof $toTest;
    }

    /**
     * Determine if given Event exists in stream & is equal.
     *
     * @param string                 $stream_id
     * @param \Framekit\Event|string $event
     *
     * @return bool
     * @throws \Framekit\Exceptions\StreamNotFound
     */
    private function hasEvent(string $stream_id, Event|string $event): bool
    {
        $deepTest = !is_string($event);
        $method = $deepTest ? 'eventDeepTest' : 'eventShallowTest';

        if ($deepTest) {
            $event->firedAt = 1;
        }

        foreach ($this->loadStream($stream_id) as $e) {
            $e->firedAt = 1;

            if ($this->{$method}($event, $e)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Wrap $event to always be array.
     *
     * @param mixed $event
     *
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
}
