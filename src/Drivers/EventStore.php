<?php

declare(strict_types=1);

namespace Framekit\Drivers;

use Framekit\Contracts\Mapper;
use Framekit\Contracts\Serializable;
use Framekit\Contracts\Serializer;
use Framekit\Contracts\Store;
use Framekit\Event;
use Framekit\Exceptions\MethodUnknown;
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
 * @license   MIT
 * @version   1.0.0
 */
final class EventStore implements Store
{
    /**
     * @var \Mrluke\Configuration\Contracts\ArrayHost
     */
    protected $config;

    /**
     * @var \Framekit\Contracts\Mapper
     */
    protected $mapper;

    /**
     * @var \Framekit\Contracts\Serializer
     */
    protected $serializer;

    /**
     * @param \Mrluke\Configuration\Contracts\ArrayHost $connection
     * @param \Framekit\Contracts\Serializer
     */
    function __construct(ArrayHost $config, Serializer $serializer, Mapper $mapper)
    {
        $this->config     = $config;
        $this->mapper     = $mapper;
        $this->serializer = $serializer;
    }

    /**
     * Store new payload in stream.
     *
     * @param string $stream_type
     * @param string $stream_id
     * @param array  $events
     *
     * @return void
     */
    public function commitToStream(string $stream_type, string $stream_id, array $events): void
    {
        DB::beginTransaction();

        $last = $this->setupDBConnection()->where('stream_id', $stream_id)
                     ->orderBy('sequence_no', 'DESC')
                     ->lockForUpdate()
                     ->first();
        $last = $last->sequence_no ?? 0;

        $common = $this->composeCommon($stream_type, $stream_id);

        foreach ($events as $e) {
            if (!$e instanceof Event) {
                DB::rollBack();

                throw new InvalidArgumentException(
                    sprintf('Projected events must be instance of %s', Event::class)
                );
            }

            $this->setupDBConnection()->insert(array_merge($common, [
                'event'       => get_class($e),
                'payload'     => $this->serializer->serialize($e),
                'version'     => $e::$eventVersion,
                'sequence_no' => ++$last,
            ]));
        }

        DB::commit();
    }

    /**
     * Load available streams.
     *
     * @return array
     */
    public function getAvailableStreams(): array
    {
        $collection = $this->setupDBConnection()->distinct()->select('stream_type', 'stream_id')
                           ->get();

        return json_decode(json_encode($collection->toArray()), true);
    }

    /**
     * Load Stream based on id.
     *
     * @param string|null $stream_id
     * @param string|null $since
     * @param string|null $till
     * @param bool        $withMeta
     *
     * @return array
     */
    public function loadStream(string $stream_id = null, ?string $since = null, ?string $till = null, bool $withMeta = false): array
    {
        $events    = [];
        $raw       = $this->getEvents($stream_id, $since, $till);
        $rowsCount = count($raw);
        for ($i = 0; $i < $rowsCount; $i++) {
            if ($this->isVersionConflict($raw[$i]->payload, $raw[$i]->version)) {
                $raw[$i]->payload = $this->mapVersion(
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
            sprintf('Trying to call unknown method [%s]. Assert methods available only in testing mode.', $name)
        );
    }

    /**
     * Compose common data for event entry.
     *
     * @param string $stream_type
     * @param string $stream_id
     *
     * @return array
     */
    protected function composeCommon(string $stream_type, string $stream_id): array
    {
        $now = now();

        return [
            'stream_type' => $stream_type,
            'stream_id'   => $stream_id,
            'meta'        => json_encode([
                'auth' => auth()->check() ? auth()->user()->id : null,
                'ip'   => request()->ip(),
            ]),
            'commited_at' => $now->toDateTimeString() . '.' . $now->micro,
        ];
    }

    /**
     * Check if version of event is actual correct.
     *
     * @param string $payload
     * @param int    $version
     *
     * @return bool
     */
    protected function isVersionConflict(string $payload, int $version): bool
    {
        $event = json_decode($payload, true);

        return $event['class']::$eventVersion != $version;
    }

    /**
     * Map old event to new version to prevent missing data.
     *
     * @param string $payload
     * @param int    $from
     * @param array  $upstream
     *
     * @return string
     */
    protected function mapVersion(string $payload, int $from, array $upstream): string
    {
        $payload = $this->mapper->map(
            json_decode($payload, true),
            $from,
            $upstream
        );

        return json_encode($payload);
    }

    /**
     * Create DB connection instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    private function setupDBConnection()
    {
        return DB::connection($this->config->get('database'))
                 ->table($this->config->get('tables.eventstore'));
    }

    /**
     * @param \Framekit\Contracts\Serializable $event
     * @param bool                             $withMeta
     * @param                                  $raw
     */
    private function loadMeta(Serializable $event, bool $withMeta, $raw): void
    {
        if ($withMeta && $event instanceof Event) {
            $meta                = json_decode($raw->meta, true);
            $meta['id']          = $raw->id;
            $meta['stream_id']   = $raw->stream_id;
            $meta['stream_type'] = $raw->stream_type;
            $meta['commited_at'] = $raw->commited_at;
            $event->__meta__     = $meta;
        }
    }

    /**
     * @param string|null $stream_id
     * @param string|null $since
     * @param string|null $till
     *
     * @return \Illuminate\Support\Collection
     */
    private function getEvents(?string $stream_id = null, ?string $since = null, ?string $till = null): Collection
    {
        $query = $this->setupDBConnection();

        if (!empty($stream_id)) {
            $query->where('stream_id', $stream_id)
                  ->orderBy('sequence_no');
        } else {
            $query->orderBy('id');
        }

        if (!empty($since)) {
            $query->where('commited_at', '>=', $since);
        }

        if (!empty($till)) {
            $query->where('commited_at', '<=', $till);
        }

        return $query->get();
    }
}
