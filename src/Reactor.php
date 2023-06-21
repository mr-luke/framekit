<?php

declare(strict_types=1);

namespace Framekit;

use Mrluke\Bus\Contracts\Handler;
use Mrluke\Bus\Contracts\Instruction;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
abstract class Reactor implements Handler
{
    /**
     * Apply event to state.
     *
     * @param \Mrluke\Bus\Contracts\Instruction $instruction
     * @return mixed
     */
    abstract public function handle(Instruction $instruction): mixed;
}
