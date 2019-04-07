<?php

declare(strict_types=1);

namespace Framekit;

use Framekit\Exceptions\MethodUnknown;
use Framekit\Event;

/**
 * Projection abstract class.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
abstract class Projection
{
    /**
     * Handle projection.
     *
     * @param  \Framekit\Event  $event
     * @return void
     */
    public function handle(Event $event): void
    {
        $class  = explode('\\', get_class($event));
        $method = 'when'. end($class);

        $this->{$method}($event);
    }

    /**
     * Capture all bad calls.
     *
     * @param  string $name
     * @param  array  $arguments
     * @return \Framekit\Exceptions\MethodUnknown
     */
    public function __call(string $name, array $arguments)
    {
        throw new MethodUnknown(
            sprintf('Trying to call unknown method [%s]', $name)
        );
    }
}
