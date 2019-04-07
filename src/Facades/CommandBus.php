<?php

namespace Framekit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @codeCoverageIgnore
 */
class CommandBus extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Framekit\Contracts\CommandBus::class;
    }
}
