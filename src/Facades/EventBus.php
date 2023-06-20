<?php

namespace Framekit\Facades;

use Illuminate\Support\Facades\Facade;

use Framekit\Contracts\EventBus as Contract;
use Framekit\Testing\EventBus as Fake;

/**
 * @method self assertReactorCalled(string $event, string $reactor)
 * @method self assertReactorHasntCalled(string $event, string $reactor)
 *
 * @method static array eventReactors()
 * @method static array globalReactors()
 * @method array handlers()
 * @method void register(array $stack)
 * @method void replace(array $stack)
 *
 * @codeCoverageIgnore
 */
class EventBus extends Facade
{
    /**
     * Create faked Projector
     *
     * @return \Framekit\Contracts\EventBus
     */
    public static function fake(): Contract
    {

        static::swap(new Fake(
            static::eventReactors(), static::globalReactors()
        ));

        return static::getFacadeRoot();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'framekit.event.bus';
    }
}
