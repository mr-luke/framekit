<?php

namespace Mrluke\Framekit\Facades;

use Illuminate\Support\Facades\Facade;

class Store extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Mrluke\Framekit\Contracts\Store::class;
    }
}
