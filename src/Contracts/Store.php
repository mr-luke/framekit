<?php

declare(strict_types=1);

namespace Framekit\Contracts;

use Carbon\Carbon;

/**
 * Store contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
interface Store
{
    /**
     * Store new payload in stream.
     *
     * @param  string $stream_type
     * @param  string $stream_id
     * @param  array  $events
     * @return void
     */
    public function commitToStream(string $stream_type, string $stream_id, array $events): void;

    /**
     * Load available streams.
     *
     * @return array
     */
    public function getAvailableStreams(): array;

    /**
     * Load Stream based on id.
     *
     * @param string|null         $stream_id
     * @param \Carbon\Carbon|null $since
     * @param \Carbon\Carbon|null $till
     * @param bool                $withMeta
     *
     * @return array
     */
    public function loadStream(string $stream_id = null, ?Carbon $since = null, ?Carbon $till = null, bool $withMeta = false): array;
}
