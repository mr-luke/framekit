<?php

namespace Mrluke\Framekit;

use Mrluke\Framekit\Event;
use Mrluke\Framekit\State;

/**
 * Reducer abstract class.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
abstract class Reducer
{
    /**
     * Apply event to state.
     *
     * @param  \Mrluke\Framekit\Event $event
     * @param  \Mrluke\Framekit\State         $state
     * @return \Mrluke\Framekit\State
     */
    abstract public static function apply(Event $event, State $state): State;

    /**
     * Trigger reaction to an event.
     *
     * @param  \Mrluke\Framekit\Event $event
     * @param  \Mrluke\Framekit\State         $state
     * @return void
     */
    public static function react(Event $event, State $state): void {}
}
