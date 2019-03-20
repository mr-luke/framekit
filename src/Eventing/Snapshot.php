<?php

namespace Mrluke\Framekit\Eventing;

use Illuminate\Support\Facades\DB;
use Mrluke\Configuration\Contracts\ArrayHost;

use Mrluke\Framekit\Contracts\Snapshot as Contract;

/**
 * Snapshot driver class for Framekit.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 * @version   1.0.0
 */
final class Snapshot implements Contract
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
     * Latest snapshot.
     *
     * @var mixed
     */
    protected $snap;

    /**
     * @param \Mrluke\Configuration\Contracts\ArrayHost $connection
     */
    function __construct(ArrayHost $config)
    {
        $this->config = $config;

        $this->setupDBConnection();
    }

    /**
     * Return last commited event seq no.
     *
     * @return string
     */
    public function getLast(): int
    {
        return $this->snap->commited;
    }

    /**
     * Return latest snapshot.
     *
     * @param  string  $stream_id
     * @return string
     */
    public function getSnapshot(string $stream_id): string
    {
        $this->snap = $this->db->where('stream_id', $stream_id)->orderBy('created_at', 'DESC')->first();

        return $this;
    }

    /**
     * Return state.
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->snap->state;
    }

    /**
     * Create DB connection instance.
     *
     * @return void
     */
    private function setupDBConnection(): void
    {
        $this->db = DB::connection($this->config->get('database'))
                      ->table($this->config->get('tables.snapshots'));
    }
}
