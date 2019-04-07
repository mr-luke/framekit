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
     * @param  string $stream_id
     * @param  array  $events
     * @return void
     */
    public function commitToStream(string $stream_id, array $events): void;

    /**
     * Load Stream based on id.
     *
     * @param  string $stream_id
     * @return array
     */
    public function loadStream(string $stream_id): array;
}
