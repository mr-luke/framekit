<?php

declare(strict_types=1);

namespace Framekit\Contracts;

/**
 * Event Mapper contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
interface Mapper
{
    /**
     * Map event to newest version.
     *
     * @param string $event
     * @param array  $payload
     * @param int    $from
     * @param array  $upstream
     * @return array
     */
    public function map(string $event, array $payload, int $from, array $upstream): array;

    /**
     * Return registered Mappers list.
     *
     * @return array
     */
    public function mappers(): array;

    /**
     * Register Reactors stack.
     *
     * @param array $stack
     * @return void
     */
    public function register(array $stack): void;
}
