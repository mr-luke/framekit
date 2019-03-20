<?php

namespace Mrluke\Framekit\Eventing;

use Illuminate\Support\Facades\DB;
use Mrluke\Configuration\Contracts\ArrayHost;

use Mrluke\Framekit\Contracts\Stream;
use Mrluke\Framekit\Event;
use Mrluke\Framekit\Exceptions\NostreamLoaded;

/**
 * EventStream driver class for Framekit.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 * @version   1.0.0
 */
final class EventStream implements Stream
{
    /**
     * Configuration of Framekit.
     *
     * @var \Mrluke\Configuration\Contracts\ArrayHost
     */
    protected $config;

    /**
     * Database driver.
     *
     * @var \Illuminate\Database\Query\Builder;
     */
    protected $db;

    /**
     * Collection of all aggragate events.
     *
     * @var array
     */
    protected $events;

    /**
     * Stream id.
     *
     * @var string
     */
    protected $stream;

    /**
     * @param \Mrluke\Configuration\Contracts\ArrayHost $connection
     */
    function __construct(ArrayHost $config)
    {
        $this->config = $config;

        $this->setupDBConnection();
    }

    /**
     * Store new payload in stream.
     *
     * @param  string $stream_id
     * @param  array  $payload
     * @return int
     */
    public function commitToStream(string $stream_id, array $payload): int
    {
        $last = $this->db->where('stream_id', $stream_id)->orderBy('commited_at', DESC)->first();
        $seq  = $last->sequence_no ?? 0;
        unset($last);

        $this->db->insert(array_merge($payload, [
            'stream_id'   => $stream_id,
            'sequence_no' => ++$seq,
            'commited_at' => now(),
        ]));

        return $seq;
    }

    /**
     * Return aggregate stream_id.
     *
     * @return string
     */
    public function getId(): string
    {
        if (empty($this->stream)) {
            throw new NostreamLoaded('Trying to get ID when no stream loaded.');
        }
        return $this->stream;
    }

    /**
     * Return iterable collection of events from Store.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return $this->events;
    }

    /**
     * Load Stream based on id.
     *
     * @param  string $stream_id
     * @return self
     */
    public function getStream(string $stream_id): Stream
    {
        $this->stream = $stream_id;

        $this->events = $this->db->where('stream_id', $stream_id)->orderBy('commited_at')->get();

        return $this;
    }

    /**
     * Create DB connection instance.
     *
     * @return void
     */
    private function setupDBConnection(): void
    {
        $this->db = DB::connection($this->config->get('database'))
                      ->table($this->config->get('tables.eventstore'));
    }
}
