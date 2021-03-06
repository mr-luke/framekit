<?php

declare(strict_types=1);

namespace Framekit\Drivers;

use Illuminate\Foundation\Application;

use Framekit\Contracts\EventBus as Bus;
use Framekit\Contracts\Publishable;
use Framekit\Exceptions\UnsupportedEvent;
use Framekit\Extentions\ClassResolver;
use Framekit\Reactor;

/**
 * EventBus is responsible for detecting reaction.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
final class EventBus implements Bus
{
    use ClassResolver;

    /**
     * Register of global Reactors.
     *
     * @var array
     */
    protected $globals;

    /**
     * Register of Event->Reactor pairs.
     *
     * @var array
     */
    protected $register;

    /**
     * @param array $stack
     */
    public function __construct(Application $app, array $stack = [], array $globals = [])
    {
        $this->app      = $app;
        $this->globals  = $globals;
        $this->register = $stack;
    }

    /**
     * Return registered global Reactors list.
     *
     * @return array
     */
    public function globalHandlers(): array
    {
        return $this->globals;
    }

    /**
     * Return registered Reactors list.
     *
     * @return array
     */
    public function handlers(): array
    {
        return $this->register;
    }

    /**
     * Handle Publishable with coresponding Handler.
     *
     * @param \Framekit\Contracts\Publishable $source
     *
     * @return void
     * @throws UnsupportedEvent
     */
    public function publish(Publishable $source): void
    {
        if (isset($this->register[get_class($source)])) {
            $this->fireEventReactors($source, $this->register[get_class($source)]);
        }

        $this->publishForGlobals($source);
    }

    /**
     * Register Reactors stack.
     *
     * @param  array $stack
     * @return void
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
     */
    public function replace(array $stack): void
    {
        $this->register = $stack;
    }

    /**
     * Handle Publishable with global Handlers.
     *
     * @param \Framekit\Contracts\Publishable $source
     *
     * @return void
     * @throws UnsupportedEvent
     */
    protected function publishForGlobals(Publishable $source): void
    {
        $this->fireEventReactors($source, $this->globals);
    }

    /**
     * Resolve and fire handler.
     *
     * @param  \Framekit\Contracts\Publishable $source
     * @param  string|array                    $destinations
     *
     * @return void
     * @throws \Framekit\Exceptions\UnsupportedEvent
     */
    private function fireEventReactors(Publishable $source, $destinations): void
    {
        if(!is_array($destinations)) {
            $destinations = [$destinations];
        }

        $this->validateReactors($destinations);

        foreach($destinations as $destination) {
            $destination->handle($source);
        }
    }

    /**
     * Resolve and vaidate reactors
     *
     * @param $destinations
     *
     * @throws \Framekit\Exceptions\UnsupportedEvent
     */
    private function validateReactors(&$destinations)
    {
        foreach($destinations as &$destination) {
            $destination = $this->resolveClass($destination);
            if (! $destination instanceof Reactor) {
                throw new UnsupportedEvent(
                    sprintf('Reactor has to extend %s', Reactor::class)
                );
            }
        }
    }
}
