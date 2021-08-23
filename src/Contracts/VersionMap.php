<?php

declare(strict_types=1);

namespace Framekit\Contracts;

/**
 * Event Version Map contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
interface VersionMap
{
    /**
     * Map event to newest version.
     *
     * @param array $payload
     * @param int   $from
     * @param int   $to
     * @param array $upstream
     * @return array
     * @throws \Framekit\Exceptions\MethodUnknown
     */
    public function translate(array $payload, int $from, int $to, array $upstream): array;
}
