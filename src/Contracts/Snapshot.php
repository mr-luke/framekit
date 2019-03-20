<?php

namespace Mrluke\Framekit\Contracts;

/**
 * Snapshot contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
interface Snapshot
{
    /**
     * Return last commited event seq no.
     *
     * @return string
     */
    public function getLast(): int;

    /**
     * Return latest snapshot.
     *
     * @param  string  $stream_id
     * @return string
     */
    public function getSnapshot(string $stream_id): string;

    /**
     * Return state.
     *
     * @return string
     */
    public function getState(): string;
}
