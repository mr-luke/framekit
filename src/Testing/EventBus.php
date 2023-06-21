<?php

declare(strict_types=1);

namespace Framekit\Testing;

use Framekit\Contracts\EventBus as Bus;
use Framekit\Event;
use Mrluke\Bus\Contracts\Process;
use PHPUnit\Framework\Assert as PHPUnit;

/**
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
    private array $globals;

    /**
     * List of published events of aggregate.
     *
     * @var array
     */
    private array $published = [];

    /**
     * Register of Event->Reactor pairs.
     *
     * @var array
     */
    private array $register;

    /**
     * @param array $stack
     * @param array $globals
     */
    public function __construct(array $stack = [], array $globals = [])
    {
        $this->globals = $globals;
        $this->register = $stack;
    }

    /**
     * Assert if given reactors have been called.
     *
     * @param string $event
     * @param string $reactor
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
     * Assert if given reactors have been called.
     *
     * @param string $event
     * @param string $reactor
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
     * @inheritDoc
     */
    public function eventReactors(): array
    {
        return $this->register;
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
     * @inheritDoc
     */
    public function globalReactors(): array
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
     * @inheritDoc
     */
    public function mapGlobals(array $stack): void
    {
        $this->globals = array_merge($this->globals, $stack);
    }

    /**
     * Handle Publishable with corresponding Handler.
     *
     * @param \Framekit\Event $event
     * @return \Mrluke\Bus\Process|null
     * @throws \Mrluke\Bus\Exceptions\InvalidAction
     */
    public function publish(Event $event): ?Process
    {
        $eventType = get_class($event);
        $reactors = $this->globals;

        if (isset($this->register[$eventType])) {
            if (!is_array($this->register[$eventType])) {
                $this->register[$eventType] = [$this->register[$eventType]];
            }

            $reactors = array_merge($reactors, $this->register[$eventType]);
        }

        foreach ($reactors as $r) {
            $this->published[$eventType][] = $r;
        }

        return count($reactors)
            ? \Mrluke\Bus\Process::create('event-bus', get_class($event), $reactors, null) : null;
    }

    /**
     * Return published Event's reactors.
     *
     * @param string|null $event
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
     * @param array $stack
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function register(array $stack): void
    {
        $this->register = array_merge($this->register, $stack);
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
        if (!is_array($this->register[$event])) {
            $this->register[$event] = [$this->register[$event]];
        }

        return (in_array($reactor, $this->globals) || in_array($reactor, $this->register[$event]))
            && in_array($reactor, $this->published[$event] ?? []);
    }
}
