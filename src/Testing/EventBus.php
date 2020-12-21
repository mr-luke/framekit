<?php

declare(strict_types=1);

namespace Framekit\Testing;

use InvalidArgumentException;
use PHPUnit\Framework\Assert as PHPUnit;

use Framekit\Contracts\EventBus as Bus;
use Framekit\Contracts\Publishable;

/**
 * EventBus is testing class.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
final class EventBus implements Bus
{
    /**
     * Register of global Reactors.
     *
     * @var array
     */
    private $globals;

    /**
     * List of published events of aggregate.
     *
     * @var array
     */
    private $published = [];

    /**
     * Register of Event->Reactor pairs.
     *
     * @var array
     */
    private $register;

    /**
     * @param array $stack
     */
    public function __construct(array $stack = [], array $globals = [])
    {
        $this->globals  = $globals;
        $this->register = $stack;
    }

    /**
     * Asssert if given reactors have been called.
     *
     * @param  string $events
     * @param  string $reactors
     * @return self
     *
     * @codeCoverageIgnore
     */
    public function assertReactorCalled(string $event, string $reactor): self
    {
        PHPUnit::assertTrue(
            $this->isCalled($event, $reactor),
            "Given reactor [{$reactor}] hasn't called for an event [{$event}]."
        );

        return $this;
    }

    /**
     * Asssert if given reactors have been called.
     *
     * @param  string $events
     * @param  string $reactors
     * @return self
     *
     * @codeCoverageIgnore
     */
    public function assertReactorHasntCalled(string $event, string $reactor): self
    {
        PHPUnit::assertFalse(
            $this->isCalled($event, $reactor),
            "Unexpected reactor [{$reactor}] called for an event [{$event}]."
        );

        return $this;
    }

    /**
     * Determine if called.
     *
     * @param string $event
     * @param string $reactor
     *
     * @return bool
     */
    private function isCalled(string $event, string $reactor): bool
    {
        if(!is_array($this->register[$event])) {
            $this->register[$event] = [$this->register[$event]];
        }

        return (in_array($reactor, $this->globals) || in_array($reactor, $this->register[$event]))
            && in_array($reactor, $this->published[$event] ?? []);
    }

    /**
     * Return registered global Reactors list.
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    public function globalHandlers(): array
    {
        return $this->globals;
    }

    /**
     * Return registered Reactors list.
     *
     * @return array
     *
     * @codeCoverageIgnore
     *
     * @codeCoverageIgnore
     */
    public function handlers(): array
    {
        return $this->register;
    }

    /**
     * Handle Publishable with coresponding Handler.
     *
     * @param  \Framekit\Contracts\Publishable $event
     * @return void
     */
    public function publish(Publishable $event): void
    {
        $eventType = get_class($event);
        $reactors  = $this->globals;

        if (isset($this->register[$eventType])) {
            if(!is_array($this->register[$eventType])) {
                $this->register[$eventType] = [$this->register[$eventType]];
            }

            $reactors = array_merge($reactors, $this->register[$eventType]);
        }

        foreach ($reactors as $r) {
            $this->published[$eventType][] = $r;
        }
    }

    /**
     * Return published Event's reactors.
     *
     * @param  string|null $event
     * @return array
     *
     * @codeCoverageIgnore
     */
    public function published(string $event = null): array
    {
        return is_null($event) ? $this->published : ($this->published[$event] ?? []);
    }

    /**
     * Register Reactors stack.
     *
     * @param  array $stack
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function register(array $stack): void
    {
        $this->register = array_merge($this->register, $stack);
    }

    /**
     * Register Reactors stack.
     *
     * @param  array $stack
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function registerGlobals(array $stack): void
    {
        $this->globals = array_merge($this->globals, $stack);
    }

    /**
     * Replace registered Reactors by given.
     *
     * @param  array  $stack
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function replace(array $stack): void
    {
        $this->register = $stack;
    }
}
