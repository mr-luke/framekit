<?php

namespace Tests\Components;

use Mrluke\Bus\Contracts\Instruction;
use Mrluke\Bus\Contracts\Handler;

use Illuminate\Http\Request;

class ResolveTest implements Handler
{
    public $class;

    function __construct(Request $class)
    {
        $this->class = $class;
    }

    /**
     * Apply event to state.
     *
     * @param \Mrluke\Bus\Contracts\Instruction $instruction
     * @return void
     */
    public function handle(Instruction $instruction) {}
}
