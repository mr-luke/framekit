<?php

declare(strict_types=1);

namespace Framekit;

use Framekit\Exceptions\MethodUnknown;
use Mrluke\Bus\Contracts\Handler;
use Mrluke\Bus\Contracts\Instruction;

/**
 * @author  Łukasz Sitnicki (mr-luke)
 * @package mr-luke/framekit
 * @link    http://github.com/mr-luke/framekit
 * @licence MIT
 */
abstract class Projection implements Handler
{
    protected bool $ignoreUnknownEvents = true;

    /**
     * Capture all bad calls.
     *
     * @param string $name
     * @param array  $arguments
     * @return void
     * @throws \Framekit\Exceptions\MethodUnknown
     */
    public function __call(string $name, array $arguments): void
    {
        if (!$this->ignoreUnknownEvents) {
            throw new MethodUnknown(
                sprintf('Trying to call unknown method [%s]', $name)
            );
        }
    }

    /**
     * Return name of method that should be invoked.
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
     * @return void
     */
    public function handle(Instruction $instruction): void
    {
        $this->{static::detectMethod($instruction)}($instruction);
    }
}
