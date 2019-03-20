<?php

namespace Mrluke\Framekit\Facades;

use Illuminate\Support\Facades\Facade;

class Handler extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Mrluke\Framekit\Contracts\Handler::class;
    }
}
