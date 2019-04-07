<?php

declare(strict_types=1);

namespace Framekit;

use Framekit\Event;

/**
 * Reactor abstract class.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
abstract class Reactor
{
    /**
     * Apply event to state.
     *
     * @param  \Framekit\Event  $event
     * @return void
     */
    abstract public function handle(Event $event): void;
}
