<?php

declare(strict_types=1);

namespace Tests\Components;

use Framekit\Reactor;
use Mrluke\Bus\Contracts\Instruction;

/**
 * DummyReactor class.
 *
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
     * @return void
     */
    public function handle(Instruction $instruction): void
    {
        $instruction->dummy();
    }
}
