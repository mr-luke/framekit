<?php

declare(strict_types=1);

namespace Framekit\Testing;

use Mrluke\Bus\Contracts\Process;
use PHPUnit\Framework\Assert as PHPUnit;

use Framekit\AggregateRoot;
use Framekit\Contracts\Projector as Contract;
use Framekit\Exceptions\MissingProjection;
use Framekit\Event;
use Framekit\Projection;

/**
 * Projector is testing class.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
final class Projector implements Contract
{
    /**
     * List of projected events of aggregate.
     *
     * @var array
     */
    private array $projected = [];

    /**
     * Register of Event->Projector pairs.
     *
     * @var array
     */
    private array $register;

    /**
     * @param array $stack
     */
    public function __construct(array $stack = [])
    {
        $this->register = $stack;
    }

    /**
     * Assert if given projections has been called.
     *
     * @param  string $aggregate
     * @param  string $projection
     * @param  string $method
     * @return self
     *
     * @codeCoverageIgnore
     */
    public function assertMethodCalled(
        string $aggregate,
        string $projection,
        string $method
    ): self {
        PHPUnit::assertTrue(
            $this->isCalled($aggregate, $projection, $method),
            "Given projection [{$projection}@{$method}] hasn't called for an aggregate [{$aggregate}]."
        );

        return $this;
    }

    /**
     * Assert if given projections has been called.
     *
     * @param  string $aggregate
     * @param  mixed  $projection
     * @param  string $method
     * @return self
     *
     * @codeCoverageIgnore
     */
    public function assertMethodHasntCalled(
        string $aggregate,
        string $projection,
        string $method
    ): self {
        PHPUnit::assertFalse(
            $this->isCalled($aggregate, $projection, $method),
            "Unexpected projection [{$projection}@{$method}] called for an aggregate [{$aggregate}]."
        );

        return $this;
    }

    /**
     * Determine if called.
     *
     * @param  string $aggregate
     * @param  string $projection
     * @param  string $method
     * @return bool
     */
    private function isCalled(string $aggregate, string $projection, string $method): bool
    {
        return ($projection == $this->register[$aggregate])
            && in_array($method, $this->projected[$aggregate] ?? []);
    }

    /**
     * Return projected Event's method list.
     *
     * @param  string|null $aggregate
     * @return array
     *
     * @codeCoverageIgnore
     */
    public function projected(string $aggregate = null): array
    {
        return is_null($aggregate) ? $this->projected : ($this->projected[$aggregate] ?? []);
    }

    /**
     * Return registered Projections list.
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function aggregateProjections(): array
    {
        return $this->register;
    }

    /**
     * @inheritDoc
     */
    public function project(AggregateRoot $aggregate): array
    {
        $aggregateClass = get_class($aggregate);

        $this->addProjectedEvents($aggregateClass, $aggregate->unpublishedEvents());

        return [];
    }

    /**
     * @inheritDoc
     */
    public function projectByEvents(AggregateRoot $aggregate, array $events): array
    {
        $this->addProjectedEvents(get_class($aggregate), $events);

        return [];
    }

    /**
     * @inheritDoc
     */
    public function projectSingle(AggregateRoot $aggregate, Event $event): Process
    {
        $this->addProjectedEvents(get_class($aggregate), [$event]);

    }

    /**
     * Register Projections stack.
     *
     * @param  array $stack
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function map(array $stack): void
    {
        $this->register = array_merge($this->register, $stack);
    }

    /**
     * Add projected events to stack.
     *
     * @param  string $aggregate
     * @param  array  $events
     * @return void
     * @throws \Framekit\Exceptions\MissingProjection
     */
    protected function addProjectedEvents(string $aggregate, array $events): void
    {
        if (!isset($this->register[$aggregate]) || empty($this->register[$aggregate])) {
            throw new MissingProjection(
                sprintf('Missing projection for aggregate %s', $aggregate)
            );
        }

        $methods = [];
        foreach ($events as $e) {
            $methods[] = Projection::detectMethod($e);
        }

        if (!isset($this->projected[$aggregate])) {
            $this->projected[$aggregate] = [];
        }
        array_push($this->projected[$aggregate], ...$methods);
    }
}
