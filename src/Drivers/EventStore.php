<?php

declare(strict_types=1);

namespace Framekit\Drivers;

use Framekit\Contracts\Mapper;
use Framekit\Contracts\Serializable;
use Framekit\Contracts\Serializer;
use Framekit\Contracts\Store;
use Framekit\Event;
use Framekit\Exceptions\MethodUnknown;
use Framekit\Exceptions\StreamNotFound;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Mrluke\Configuration\Contracts\ArrayHost;

/**
 * EventStream driver class for Framekit.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
final class EventStore implements Store
{
    /**
     * @var \Mrluke\Configuration\Contracts\ArrayHost
     */
    protected ArrayHost $config;

    /**
     * @var \Framekit\Contracts\Mapper
     */
    protected Mapper $mapper;

    /**
     * @var \Framekit\Contracts\Serializer
     */
    protected Serializer $serializer;

    /**
     * @param \Mrluke\Configuration\Contracts\ArrayHost $config
     * @param \Framekit\Contracts\Serializer            $serializer
     * @param \Framekit\Contracts\Mapper                $mapper
     */
    function __construct(ArrayHost $config, Serializer $serializer, Mapper $mapper)
    {
        $this->config = $config;
        $this->mapper = $mapper;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function commitToStream(string $streamType, string $streamId, array $events): void
    {
        DB::beginTransaction();

        $last = $this->setupDBConnection()->where('stream_id', $streamId)
            ->orderBy('sequence_no', 'DESC')
            ->lockForUpdate()
            ->first();
        $last = $last->sequence_no ?? 0;

        $common = $this->composeCommon($streamType, $streamId);

        foreach ($events as $e) {
            if (!$e instanceof Event) {
                DB::rollBack();

                throw new InvalidArgumentException(
                    sprintf('Projected events must be instance of %s', Event::class)
                );
            }

            $this->setupDBConnection()->insert(
                array_merge(
                    $common,
                    [
                        'event' => get_class($e),
                        'payload' => $this->serializer->serialize($e),
                        'version' => $e::$__eventVersion__,
                        'sequence_no' => ++$last,
                    ]
                )
            );
        }

        DB::commit();
    }

    /**
     * @inheritDoc
     */
    public function getAvailableStreams(): array
    {
        $collection = $this->setupDBConnection()->distinct()->select('stream_type', 'stream_id')
            ->get();

        return json_decode(json_encode($collection->toArray()), true);
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
        if (!$this->checkIfStreamExists($streamId)) {
            throw new StreamNotFound(
                sprintf('Stream [%s] not found', $streamId)
            );
        }

        $events = [];
        $raw = $this->getEvents($streamId, $since, $till);
        $rowsCount = count($raw);
        for ($i = 0; $i < $rowsCount; $i++) {
            if ($this->isVersionConflict($raw[$i]->payload, $raw[$i]->version)) {
                $raw[$i]->payload = $this->mapVersion(
                    $raw[$i]->event,
                    $raw[$i]->payload,
                    $raw[$i]->version,
                    array_slice($raw->toArray(), 0, $i + 1)
                );
            }

            $event = $this->serializer->unserialize($raw[$i]->payload);
            $this->loadMeta($event, $withMeta, $raw[$i]);
            $events[] = $event;
        }

        return $events;
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
        $this->setupDBConnection()->where('id', $eventId)->update(
            array_filter([
                'event' => $event,
                'payload' => $payload,
                'sequence_no' => $seqNo,
            ])
        );
    }

    /**
     * Capture all bad calls.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return void
     * @throws \Framekit\Exceptions\MethodUnknown
     */
    public function __call(string $name, array $arguments)
    {
        throw new MethodUnknown(
            sprintf(
                'Trying to call unknown method [%s]. Assert methods available only in testing mode.',
                $name
            )
        );
    }

    /**
     * Check if stream exists.
     *
     * @param string $streamId
     * @return bool
     */
    protected function checkIfStreamExists(string $streamId): bool
    {
        return $this->setupDBConnection()->where('stream_id', $streamId)->exists();
    }

    /**
     * Compose common data for event entry.
     *
     * @param string $streamType
     * @param string $streamId
     *
     * @return array
     */
    protected function composeCommon(string $streamType, string $streamId): array
    {
        $now = now();

        return [
            'stream_type' => $streamType,
            'stream_id' => $streamId,
            'meta' => json_encode(
                [
                    'auth' => auth()->check() ? auth()->user()->id : null,
                    'ip' => request()->ip(),
                ]
            ),
            'committed_at' => $now->toDateTimeString() . '.' . $now->micro,
        ];
    }

    /**
     * Check if version of event is actual correct.
     *
     * @param string $payload
     * @param int    $version
     * @return bool
     */
    protected function isVersionConflict(string $payload, int $version): bool
    {
        $event = json_decode($payload, true);
        $class = $event['class'];

        return $class::$__eventVersion__ != $version;
    }

    /**
     * Map old event to new version to prevent missing data.
     *
     * @param string $event
     * @param string $payload
     * @param int    $from
     * @param array  $upstream
     *
     * @return string
     */
    protected function mapVersion(
        string $event,
        string $payload,
        int    $from,
        array  $upstream
    ):
    string {
        $payload = $this->mapper->map(
            $event,
            json_decode($payload, true),
            $from,
            $upstream
        );

        return json_encode($payload);
    }

    /**
     * @param string|null $streamId
     * @param string|null $since
     * @param string|null $till
     *
     * @return \Illuminate\Support\Collection
     */
    private function getEvents(
        ?string $streamId = null,
        ?string $since = null,
        ?string $till = null
    ): Collection {
        $query = $this->setupDBConnection();

        if (!empty($streamId)) {
            $query->where('stream_id', $streamId)
                ->orderBy('sequence_no');
        } else {
            $query->orderBy('id');
        }

        if (!empty($since)) {
            $query->where('committed_at', '>=', $since);
        }

        if (!empty($till)) {
            $query->where('committed_at', '<=', $till);
        }

        return $query->get();
    }

    /**
     * @param \Framekit\Contracts\Serializable $event
     * @param bool                             $withMeta
     * @param                                  $raw
     */
    private function loadMeta(Serializable $event, bool $withMeta, $raw): void
    {
        if ($withMeta && $event instanceof Event) {
            $meta = json_decode($raw->meta, true);
            $meta['id'] = $raw->id;
            $meta['stream_id'] = $raw->stream_id;
            $meta['stream_type'] = $raw->stream_type;
            $meta['committed_at'] = $raw->committed_at;
            $event->__meta__ = $meta;
        }
    }

    /**
     * Create DB connection instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    private function setupDBConnection(): Builder
    {
        return DB::connection($this->config->get('database'))
            ->table($this->config->get('tables.eventstore'));
    }
}
