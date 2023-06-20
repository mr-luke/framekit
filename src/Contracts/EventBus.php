<?php

declare(strict_types=1);

namespace Framekit\Contracts;

use Mrluke\Bus\Contracts\Process;

use Framekit\Event;

/**
 * @author  Łukasz Sitnicki (mr-luke)
 * @package mr-luke/framekit
 * @link    http://github.com/mr-luke/framekit
 * @licence MIT
 */
interface EventBus
{
    /**
     * Return all registered event reactors.
     *
     * @return array
     */
    public function eventReactors(): array;

    /**
     * Return registered global Reactors list.
     *
     * @return array
     */
    public function globalReactors(): array;

    /**
     * Register Reactors stack.
     *
     * @param array $stack
     * @return void
     * @throws \Mrluke\Bus\Exceptions\InvalidHandler
     * @throws \ReflectionException
     */
    public function mapGlobals(array $stack): void;

    /**
     * Publish Event to its reactors.
     *
     * @param \Framekit\Event $event
     * @return \Mrluke\Bus\Contracts\Process|null
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Mrluke\Bus\Exceptions\InvalidAction
     * @throws \Mrluke\Bus\Exceptions\InvalidHandler
     * @throws \Mrluke\Bus\Exceptions\MissingConfiguration
     * @throws \Mrluke\Bus\Exceptions\MissingHandler
     * @throws \Mrluke\Bus\Exceptions\RuntimeException
     * @throws \ReflectionException
     */
    public function publish(Event $event): ?Process;
}
