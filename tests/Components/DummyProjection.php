<?php

declare(strict_types=1);

namespace Tests\Components;

use Mrluke\Bus\Contracts\Instruction;

use Framekit\Projection;

/**
 * DummyProjection class.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class DummyProjection extends Projection
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
