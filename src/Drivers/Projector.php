<?php

declare(strict_types=1);

namespace Framekit\Drivers;

use Illuminate\Foundation\Application;
use InvalidArgumentException;

use Framekit\AggregateRoot;
use Framekit\Projection;
use Framekit\Contracts\Projector as Contract;
use Framekit\Extentions\ClassResolver;
use Framekit\Exceptions\MethodUnknown;
use Framekit\Exceptions\MissingProjection;
use Framekit\Event;

/**
 * Projector is responsible for projectiong cahnges to DB.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
final class Projector implements Contract
{
    use ClassResolver;

    /**
     * Register of Event->Reactor pairs.
     *
     * @var array
     */
    protected $register;

    /**
     * @param \Illuminate\Foundation\Application  $app
     * @param array                               $stack
     */
    public function __construct(Application $app, array $stack = [])
    {
        $this->app      = $app;
        $this->register = $stack;
    }

    /**
     * Return registered Projections list.
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    public function projections(): array
    {
        return $this->register;
    }

    /**
     * Project changes for given aggregate.
     *
     * @param  \Framekit\AggregateRoot  $aggregate
     * @param  array                    $events
     * @return void
     */
    public function project(AggregateRoot $aggregate, array $events): void
    {
        $projection = $this->getProjection(get_class($aggregate));

        foreach ($events as $e) {
            if (! $e instanceof Event) {
                throw new InvalidArgumentException(
                    sprintf('Projected events must be instance of %s', Event::class)
                );
            }

            $projection->handle($e);
        }
    }

    /**
     * Register Projections stack.
     *
     * @param  array $stack
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function register(array $stack): void
    {
        $this->register = array_merge($this->register, $stack);
    }

    /**
     * Return aggregate's projection.
     *
     * @param  string  $aggregate
     * @return \Framekit\Projection
     *
     * @throws \Framekit\Exceptions\MissingProjection
     */
    protected function getProjection(string $aggregate): Projection
    {
        if (!isset($this->register[$aggregate]) || empty($this->register[$aggregate])) {
            throw new MissingProjection(
                sprintf('Missing projection for aggregate %s', $aggregate)
            );
        }

        return $this->resolveClass($this->register[$aggregate]);
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
            sprintf('Trying to call unknown method [%s]. Assert methods available only in testing mode.', $name)
        );
    }
}
