<?php

namespace Framekit\Facades;

use Illuminate\Support\Facades\Facade;

use Framekit\Contracts\Projector as Contract;
use Framekit\Testing\Projector as Fake;

/**
 * @method self assertMethodCalled(string $aggregate, string $projection, string $method)
 * @method self assertMethodHasntCalled(string $aggregate, string $projection, string $method)
 *
 * @method static array aggregateProjections()
 * @method void register(array $stack)
 *
 * @codeCoverageIgnore
 */
class Projector extends Facade
{
    /**
     * Create faked Projector
     *
     * @return \Framekit\Contracts\Projector
     */
    public static function fake(): Contract
    {
        static::swap(new Fake(
            static::aggregateProjections()
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
        return 'framekit.projector';
    }
}
