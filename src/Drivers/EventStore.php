<?php

declare(strict_types=1);

namespace Framekit\Drivers;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Mrluke\Configuration\Contracts\ArrayHost;

use Framekit\Contracts\Serializer;
use Framekit\Contracts\Store;
use Framekit\Event;

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
     * @var \Framekit\Contracts\Serializer
     */
    protected $serializer;

    /**
     * @param \Mrluke\Configuration\Contracts\ArrayHost $connection
     * @param \Framekit\Contracts\Serializer
     */
    function __construct(ArrayHost $config, Serializer $serializer)
    {
        $this->config     = $config;
        $this->serializer = $serializer;
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
        $last = $this->setupDBConnection()->where('stream_id', $stream_id)
                                          ->orderBy('commited_at', 'DESC')
                                          ->first();
        $last = $last->sequence_no ?? 0;

        DB::beginTransaction();

        $common = $this->composeCommon($stream_id);

        foreach ($events as $e) {
            if (! $e instanceof Event) {
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
     * Load Stream based on id.
     *
     * @param  string $stream_id
     * @return array
     */
    public function loadStream(string $stream_id): array
    {
        $events = [];
        $raw    = $this->setupDBConnection()->where('stream_id', $stream_id)
                                            ->orderBy('commited_at')
                                            ->get();

        foreach ($raw as $r) {
            if ($this->isVersionConflict($r->payload, $r->version)) {
                $r->payload = $this->mapVersion($r->payload);
            }

            $events[] = $this->serializer->unserialize($r->payload);
        }

        return $events;
    }

    /**
     * Compose common data for event entry.
     *
     * @param  string $stream_id
     * @return array
     */
    protected function composeCommon(string $stream_id): array
    {
        return [
            'stream_id'   => $stream_id,
            'meta'        => json_encode([
                'auth' => auth()->check() ? auth()->user()->id : null,
                'ip'   => request()->ip(),
            ]),
            'commited_at' => now(),
        ];
    }

    /**
     * Check if version of event is actual correct.
     *
     * @param  string  $event
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
     * @param  string $payload
     * @return string
     */
    protected function mapVersion(string $payload): string
    {
        // TODO!
        return $payload;
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
}
