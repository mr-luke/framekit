<?php

declare(strict_types=1);

namespace Framekit\Contracts;

use Framekit\AggregateRoot;
use Framekit\Event;

/**
 * Projector contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
interface Projector
{
    /**
     * Return registered Projections list.
     *
     * @return array
     */
    public function projections(): array;

    /**
     * Project changes for given aggregate.
     *
     * @param  \Framekit\AggregateRoot  $aggregate
     * @param  array                    $events
     * @return void
     */
    public function project(AggregateRoot $aggregate, array $events): void;

    /**
     * Project changes for given aggregate.
     *
     * @param  string          $aggregate
     * @param  \Framekit\Event  $event
     * @return void
     */
    public function projectByEvent(string $aggregate, Event $event): void;

    /**
     * Register Projections stack.
     *
     * @param  array $stack
     * @return void
     */
    public function register(array $stack): void;
}
