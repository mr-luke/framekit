<?php

declare(strict_types=1);

namespace Tests\Components;

use Framekit\Reactor;
use Mrluke\Bus\Contracts\Instruction;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class DummyReactor extends Reactor
{
    /**
     * Handle projection.
     *
     * @param \Mrluke\Bus\Contracts\Instruction $instruction
     * @return mixed
     */
    public function handle(Instruction $instruction): mixed
    {
        $instruction->dummy();
    }
}
