<?php

declare(strict_types=1);

namespace Framekit\Contracts;

use Framekit\AggregateRoot;
use Framekit\Event;
use Mrluke\Bus\Contracts\Bus;
use Mrluke\Bus\Contracts\Process;

/**
 * Projector contract.
 *
 * @author  Łukasz Sitnicki (mr-luke)
 * @package mr-luke/framekit
 * @link    http://github.com/mr-luke/framekit
 * @licence MIT
 * @version 2.0.0
 */
interface Projector extends Bus
{
    /**
     * Project changes for given aggregate.
     *
     * @param \Framekit\AggregateRoot $aggregate
     * @param array                   $events
     * @return \Mrluke\Bus\Contracts\Process
     */
    public function project(AggregateRoot $aggregate, array $events): Process;

    /**
     * Project changes for given aggregate.
     *
     * @param string          $aggregate
     * @param \Framekit\Event $event
     * @return \Mrluke\Bus\Contracts\Process
     */
    public function projectByEvent(string $aggregate, Event $event): Process;
}
