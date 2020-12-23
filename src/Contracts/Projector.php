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
