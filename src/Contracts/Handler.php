<?php

namespace Mrluke\Framekit\Contracts;

use Mrluke\Framekit\Event;
use Mrluke\Framekit\State;

/**
 * EventHandler contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
interface Handler
{
    /**
     * Apply event to a state.
     *
     * @param  \Mrluke\Framekit\Event  $event
     * @param  \Mrluke\Framekit\State          $state
     * @return \Mrluke\Framekit\State
     */
    public function applyOnState(Event $event, State $state): State;

    /**
     * Fire new stream's event.
     *
     * @param  \Mrluke\Framekit\Event  $event
     * @param  \Mrluke\Framekit\State          $state
     * @return \Mrluke\Framekit\State
     */
    public function fireOnState(Event $event, State $state): State;

    /**
     * Register Event-Reducer stack.
     *
     * @param  array $stack
     * @return void
     */
    public function register(array $stack): void;

    /**
     * Replace registered reducers by given.
     *
     * @param  array  $stack
     * @return void
     */
    public function replace(array $stack): void;
}
