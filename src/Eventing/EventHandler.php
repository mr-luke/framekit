<?php

namespace Mrluke\Framekit\Eventing;

use ReflectionClass;
use Mrluke\Framekit\Contracts\Handler;
use Mrluke\Framekit\Event;
use Mrluke\Framekit\Exceptions\UnregisteredEvent;
use Mrluke\Framekit\Exceptions\UnsupportedEvent;
use Mrluke\Framekit\Reducer;
use Mrluke\Framekit\State;

/**
 * EventHandler is responsible for handling event's state mutation.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
final class EventHandler implements Handler
{
    /**
     * Register of Event->Reducer pairs.
     *
     * @var array
     */
    protected $register;

    /**
     * Apply event to a state without reacting.
     *
     * @param  \Mrluke\Framekit\Event  $event
     * @param  \Mrluke\Framekit\State          $state
     * @return \Mrluke\Framekit\State
     */
    public function applyOnState(Event $event, State $state): State
    {
        $reducer = $this->getReducer($event);

        return $reducer::apply($event, $state);
    }

    /**
     * Fire new stream event with reaction.
     *
     * @param  \Mrluke\Framekit\Event $event
     * @param  \Mrluke\Framekit\State         $state
     * @return \Mrluke\Framekit\State
     */
    public function fireOnState(Event $event, State $state): State
    {
        $reducer = $this->getReducer($event);

        $mutated = $reducer::apply($event, $state);

        $reducer::react($event, $mutated);

        return $mutated;
    }

    /**
     * Register Event-Reducer stack.
     *
     * @param  array $stack
     * @return void
     */
    public function register(array $stack): void
    {
        $this->register = array_merge($this->register, $stack);
    }

    /**
     * Replace registered reducers by given.
     *
     * @param  array  $stack
     * @return void
     */
    public function replace(array $stack): void
    {
        $this->register = $stack;
    }

    /**
     * Detect Reducer assigned to given Event.
     *
     * @param  \Mrluke\Framekit\Event  $event
     * @return string
     *
     * @throws \Mrluke\Framekit\Exceptions\UnregisteredEvent
     */
    protected function detectReducer(Event $event): string
    {
        $eventClass = get_class($event);

        if (! isset($this->register[$eventClass])) {
            throw new UnregisteredEvent(
                sprintf('Given event [%s] is not registered.', $eventClass)
            );
        }

        return $this->register[$eventClass];
    }

    /**
     * Instantiate Reducer class.
     *
     * @param  \Mrluke\Framekit\Event $event
     * @return string
     *
     * @throws \Mrluke\Framekit\Exceptions\UnregisteredEvent
     * @throws \Mrluke\Framekit\Exceptions\UnsupportedEvent
     */
    protected function getReducer(Event $event): string
    {
        $reducer = $this->detectReducer($event);

        $reflection = new ReflectionClass($reducer);

        if (! $reflection->isInstantiable() || ! $reflection->isSubclassOf(Reducer::class)) {
            throw new UnsupportedEvent(
                sprintf('Reducer has to extend %s', Reducer::class)
            );
        }

        return $reducer;
    }
}
