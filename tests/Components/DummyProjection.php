<?php

declare(strict_types=1);

namespace Tests\Components;

use Mrluke\Bus\Contracts\Instruction;

use Framekit\Projection;

/**
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
     * @return mixed
     */
    public function handle(Instruction $instruction): mixed
    {
        $instruction->dummy();
    }
}
