<?php

namespace Mrluke\Framekit\Contracts;

use IteratorAggregate;

/**
 * Stream contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
interface Stream extends IteratorAggregate
{
    /**
     * Store new payload in stream.
     *
     * @param  string $stream_id
     * @param  array  $payload
     * @return int
     */
    public function commitToStream(string $stream_id, array $payload): int;

    /**
     * Return aggregate stream_id.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Load Stream based on id.
     *
     * @param  string $stream_id
     * @return self
     */
    public function getStream(string $stream_id): self;
}
