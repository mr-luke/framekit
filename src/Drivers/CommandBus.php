<?php

declare(strict_types=1);

namespace Framekit\Drivers;

use Illuminate\Foundation\Application;

use Framekit\Contracts\CommandBus as Bus;
use Framekit\Contracts\Publishable;
use Framekit\Exceptions\MissingHandler;
use Framekit\Extentions\ClassResolver;
use Framekit\Handler;

/**
 * EventBus is responsible for detecting reaction.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
final class CommandBus implements Bus
{
    use ClassResolver;

    /**
     * Register of Event->Reactor pairs.
     *
     * @var array
     */
    protected $register;

    /**
     * @param array $stack
     */
    public function __construct(Application $app, array $stack = [])
    {
        $this->app      = $app;
        $this->register = $stack;
    }

    /**
     * Return registered Reducers list.
     *
     * @return array
     */
    public function handlers(): array
    {
        return $this->register;
    }

    /**
     * Handle Publishable with coresponding Handler.
     *
     * @param  \Framekit\Contracts\Publishable  $source
     * @return void
     */
    public function publish(Publishable $source): void
    {
        $handler = $this->getHandler(get_class($source));
        $handler->handle($source);
    }

    /**
     * Register Reactors stack.
     *
     * @param  array $stack
     * @return void
     */
    public function register(array $stack): void
    {
        $this->register = array_merge($this->register, $stack);
    }

    /**
     * Return aggregate's projection.
     *
     * @param  string  $command
     * @return \Framekit\Handler
     *
     * @throws \Framekit\Exceptions\MissingProjection
     */
    protected function getHandler(string $command): Handler
    {
        if (!isset($this->register[$command]) || empty($this->register[$command])) {
            throw new MissingHandler(
                sprintf('Missing handler for command %s', $command)
            );
        }

        return $this->resolveClass($this->register[$command]);
    }
}
