<?php

declare(strict_types=1);

namespace Framekit;

use Mrluke\Bus\Contracts\Handler;
use Mrluke\Bus\Contracts\Instruction;

use Framekit\Exceptions\MethodUnknown;

/**
 * Projection abstract class.
 *
 * @author  Łukasz Sitnicki (mr-luke)
 * @package mr-luke/framekit
 * @link    http://github.com/mr-luke/framekit
 * @licence MIT
 * @version 2.0.0
 */
abstract class Projection implements Handler
{
    /**
     * Return name of method that should be invoke.
     *
     * @param \Mrluke\Bus\Contracts\Instruction $instruction
     * @return string
     * @codeCoverageIgnore
     */
    public static function detectMethod(Instruction $instruction): string
    {
        $namespace = explode('\\', get_class($instruction));

        return 'when' . end($namespace);
    }

    /**
     * Handle projection.
     *
     * @param \Mrluke\Bus\Contracts\Instruction $instruction
     * @return mixed
     */
    public function handle(Instruction $instruction)
    {
        return $this->{static::detectMethod($instruction)}($instruction);
    }

    /**
     * Capture all bad calls.
     *
     * @param string $name
     * @param array  $arguments
     * @return void
     * @throws \Framekit\Exceptions\MethodUnknown
     */
    public function __call(string $name, array $arguments)
    {
        throw new MethodUnknown(
            sprintf('Trying to call unknown method [%s]', $name)
        );
    }
}
