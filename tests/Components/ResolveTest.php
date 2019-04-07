<?php

namespace Tests\Components;

use Framekit\Contracts\Command;
use Framekit\Handler;
use Illuminate\Http\Request;

class ResolveTest extends Handler
{
    public $class;

    function __construct(Request $class)
    {
        $this->class = $class;
    }

    /**
     * Apply event to state.
     *
     * @param  \Framekit\Contracts\Command  $command
     * @return void
     */
    public function handle(Command $command): void
    {

    }
}
