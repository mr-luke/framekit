<?php

declare(strict_types=1);

namespace Framekit\Contracts;

use Mrluke\Bus\Contracts\Bus;
use Mrluke\Bus\Contracts\Process;

use Framekit\Event;

/**
 * EventBus contract.
 *
 * @author  Łukasz Sitnicki (mr-luke)
 * @package mr-luke/framekit
 * @link    http://github.com/mr-luke/framekit
 * @licence MIT
 * @version 2.0.0
 */
interface EventBus extends Bus
{
    /**
     * Return registered global Reactors list.
     *
     * @return array
     * @throws \Mrluke\Bus\Exceptions\InvalidHandler
     * @throws \ReflectionException
     */
    public function globalHandler(): array;

    /**
     * Register Reactors stack.
     *
     * @param array $stack
     * @return void
     */
    public function mapGlobals(array $stack): void;

    /**
     * Publish Event to it's reactors.
     *
     * @param \Framekit\Event $event
     * @param bool            $cleanOnSuccess
     * @return \Mrluke\Bus\Contracts\Process
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Mrluke\Bus\Exceptions\InvalidAction
     * @throws \Mrluke\Bus\Exceptions\InvalidHandler
     * @throws \Mrluke\Bus\Exceptions\MissingConfiguration
     * @throws \Mrluke\Bus\Exceptions\MissingHandler
     * @throws \ReflectionException
     */
    public function publish(Event $event, bool $cleanOnSuccess = false): Process;
}
