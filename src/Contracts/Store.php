<?php

declare(strict_types=1);

namespace Framekit\Contracts;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
interface Store
{
    /**
     * Store new payload in stream.
     *
     * @param string $streamType
     * @param string $streamId
     * @param array  $events
     * @return void
     */
    public function commitToStream(string $streamType, string $streamId, array $events): void;

    /**
     * Load available streams.
     *
     * @return array
     */
    public function getAvailableStreams(): array;

    /**
     * Load Stream based on id.
     *
     * @param string|null $streamId
     * @param string|null $since
     * @param string|null $till
     * @param bool        $withMeta
     * @return array
     * @throws \Framekit\Exceptions\StreamNotFound
     */
    public function loadStream(
        string  $streamId = null,
        ?string $since = null,
        ?string $till = null,
        bool    $withMeta = false
    ): array;

    /**
     * Load raw Stream based on id.
     *
     * @param string|null $streamId
     * @param string|null $since
     * @param string|null $till
     * @return array
     * @throws \Framekit\Exceptions\StreamNotFound
     */
    public function loadRawStream(
        string  $streamId = null,
        ?string $since = null,
        ?string $till = null
    ): array;

    /**
     * Override a single Event.
     *
     * @param int         $eventId
     * @param string|null $event
     * @param array|null  $payload
     * @param int|null    $seqNo
     * @return void
     * @throws \Exception
     */
    public function overrideEvent(
        int    $eventId,
        string $event = null,
        array  $payload = null,
        int    $seqNo = null
    ): void;

    /**
     * Replace whole stream to a given one.
     *
     * @param string $streamId
     * @param array  $stream
     * @return void
     * @throws \Exception
     */
    public function replaceStream(string $streamId, array $stream): void;
}
