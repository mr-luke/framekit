<?php

declare(strict_types=1);

namespace Framekit;

use Framekit\Contracts\Command;

/**
 * CommandHandler abstract class.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
abstract class Handler
{
    /**
     * Apply event to state.
     *
     * @param  \Framekit\Contracts\Command  $command
     * @return void
     */
    abstract public function handle(Command $command): void;
}
