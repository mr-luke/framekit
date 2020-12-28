<?php

declare(strict_types=1);

namespace Framekit\Contracts;

use Mrluke\Bus\Contracts\Process;

use Framekit\AggregateRoot;
use Framekit\Event;

/**
 * Projector contract.
 *
 * @author  Łukasz Sitnicki (mr-luke)
 * @package mr-luke/framekit
 * @link    http://github.com/mr-luke/framekit
 * @licence MIT
 * @version 2.0.0
 */
interface Projector
{
    /**
     * Return registered Projections list.
     *
     * @return array
     */
    public function aggregateProjections(): array;

    /**
     * Project changes for given aggregate.
     *
     * @param \Framekit\AggregateRoot $aggregate
     * @return \Mrluke\Bus\Contracts\Process[]
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Mrluke\Bus\Exceptions\InvalidAction
     * @throws \Mrluke\Bus\Exceptions\InvalidHandler
     * @throws \Mrluke\Bus\Exceptions\MissingConfiguration
     * @throws \Mrluke\Bus\Exceptions\MissingHandler
     * @throws \Mrluke\Bus\Exceptions\MissingProcess
     * @throws \ReflectionException
     */
    public function project(AggregateRoot $aggregate): array;

    /**
     * Project changes for given aggregate.
     *
     * @param \Framekit\AggregateRoot $aggregate
     * @param array                   $events
     * @return \Mrluke\Bus\Contracts\Process[]
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Mrluke\Bus\Exceptions\InvalidAction
     * @throws \Mrluke\Bus\Exceptions\InvalidHandler
     * @throws \Mrluke\Bus\Exceptions\MissingConfiguration
     * @throws \Mrluke\Bus\Exceptions\MissingHandler
     * @throws \Mrluke\Bus\Exceptions\MissingProcess
     * @throws \ReflectionException
     */
    public function projectByEvents(AggregateRoot $aggregate, array $events): array;

    /**
     * Project changes for given aggregate.
     *
     * @param \Framekit\AggregateRoot $aggregate
     * @param \Framekit\Event         $event
     * @return \Mrluke\Bus\Contracts\Process
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Mrluke\Bus\Exceptions\InvalidAction
     * @throws \Mrluke\Bus\Exceptions\InvalidHandler
     * @throws \Mrluke\Bus\Exceptions\MissingConfiguration
     * @throws \Mrluke\Bus\Exceptions\MissingHandler
     * @throws \Mrluke\Bus\Exceptions\MissingProcess
     * @throws \ReflectionException
     */
    public function projectSingle(AggregateRoot $aggregate, Event $event): Process;
}
