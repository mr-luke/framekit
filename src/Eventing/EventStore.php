<?php

namespace Mrluke\Framekit\Eventing;

use InvalidArgumentException;
use Mrluke\Configuration\Contracts\ArrayHost;

use Mrluke\Framekit\Aggregate;
use Mrluke\Framekit\Contracts\Snapshot;
use Mrluke\Framekit\Contracts\Stream;
use Mrluke\Framekit\Event;
use Mrluke\Framekit\State;

/**
 * EventStore driver class for Framekit.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 * @version   1.0.0
 */
class EventStore implements Store
{
    /**
     * Configuration of Framekit.
     *
     * @var \Mrluke\Configuration\Contracts\ArrayHost
     */
    protected $config;

    /**
     * Instances of serializers.
     *
     * @var array
     */
    protected $serializers;

    /**
     * @param \Mrluke\Configuration\Contracts\ArrayHost $config
     * @param \Mrluke\Framekit\Contracts\Stream         $stream
     * @param \Mrluke\Framekit\Contracts\Snapshot       $snapshot
     */
    function __construct(ArrayHost $config, Stream $stream, Snapshot $snapshot)
    {
        $this->config   = $config;
        $this->stream   = $stream;
        $this->snapshot = $snapshot;

        $this->setupSerializers();
    }

    /**
     * Retrive aggregate's snapshot from Store.
     *
     * @param  string  $stream_id
     * @return \Mrluke\Framekit\State|null
     */
    public function getAggregateSnapshot(string $stream_id): ?State
    {
        return $this->snapshot->getSnapshot($stream_id);
    }

    /**
     * Retrive aggregate's event stream from Store.
     *
     * @param  string  $stream_id
     * @return \Mrluke\Framekit\Contracts\Stream
     */
    public function getAggregateStream(string $stream_id): Stream
    {
        return $this->stream->getStream($stream_id);
    }

    /**
     * Reduild aggraget state based on snapshot & stream.
     *
     * @param  string  $stream_id
     * @param  string  $aggreagate
     * @return \Mrluke\Framekit\Aggregate
     */
    public function rebuildAggregate(string $stream_id, string $aggreagate): Aggregate
    {
        $snapshot = $this->getAggregateSnapshot($stream_id);
        $state    = $this->unserialize($snapshot->getState(), 'state');

        return $aggreagate::rebuildState(
            $this->getAggregateStream($stream_id),
            $state,
            $snapshot->getLast()
        );
    }

    /**
     * Serialize object to string.
     *
     * @param  \Mrluke\Framekit\Contracts\Sarializable  $toSerialize
     * @param  string                                   $subject
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function serialize(Sarializable $toSerialize, string $subject): string
    {
        if (!in_array($subject, ['event', 'state'])) {
            throw new InvalidArgumentException(
                sprintf('Given subject [%s] is not supported.', $subject)
            );
        }

        return $this->serializers[$subject]->serialize($toSerialize);
    }

    /**
     * Store new event in store.
     *
     * @param  \Mrluke\Framekit\Event  $event
     * @param  string                                    $stream_id
     * @return int
     */
    public function storeEvent(Event $event, string $stream_id): int
    {
        $event   = get_class($event);
        $payload = $this->serialize($event, 'event');
        $meta    = $this->composeMeta($event);

        return $this->stream->commitToStream($stream_id, compact('event', 'payload', 'meta'));
    }

    /**
     * Unserialize to object.
     *
     * @param  string  $serialized
     * @param  string  $subject
     * @return \Mrluke\Framekit\Contracts\Serializable
     *
     * @throws \InvalidArgumentException
     */
    public function unserialize(string $serialized, string $subject): Serializable
    {
        if (!in_array($subject, ['event', 'state'])) {
            throw new InvalidArgumentException(
                sprintf('Given subject [%s] is not supported.', $subject)
            );
        }

        return $this->serializers[$subject]->unserialize($serialized);
    }

    /**
     * Composer meta attributes array.
     *
     * @param  \Mrluke\Framekit\Event  $event
     * @return array
     */
    protected function composeMeta(Event $event): array
    {
        return [
            'version' => $event->version ?? 1,
            'user'    => optional(request()->user())->id,
            'ip'      => request()->ip,
        ];
    }

    /**
     * Setup serializers based on configuration.
     *
     * @return void
     */
    private function setupSerializers(): void
    {
        foreach ($this->config->get('drivers') as $k => $d) {
            $this->serializers[$k] = new $d;
        }
    }
}
