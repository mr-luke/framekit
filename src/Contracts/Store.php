<?php

declare(strict_types=1);

namespace Framekit\Contracts;

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
     * @param string $stream_type
     * @param string $stream_id
     * @param array  $events
     *
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
     * @param string|null $stream_id
     * @param string|null $since
     * @param string|null $till
     * @param bool        $withMeta
     *
     * @return array
     */
    public function loadStream(string $stream_id = null, ?string $since = null, ?string $till = null, bool $withMeta = false): array;
}
