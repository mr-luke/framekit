<?php

declare(strict_types=1);

namespace Framekit\Extentions;

use Carbon\Carbon;
use Framekit\AggregateRoot;
use Framekit\Event;
use Framekit\Events\AggregateCreated;
use Framekit\Events\AggregateRemoved;
use Framekit\Exceptions\MethodUnknown;

/**
 * Event Sourcing extension for Aggregate.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
trait EventSourcedAggregate
{
    /**
     * @var array
     */
    protected $aggregatedEvents = [];

    /**
     * @var int
     */
    protected $aggregateVersion = 0;

    /**
     * Return actual version of an Aggregate
     *
     * @return int
     */
    public function getVersion(): int
    {
        return $this->aggregateVersion;
    }

    /**
     * Create new aggregate with Event.
     *
     * @param string $aggregateId
     * @return \Framekit\AggregateRoot
     */
    public static function create(string $aggregateId): AggregateRoot
    {
        $aggregate = new static($aggregateId);
        $aggregate->fireEvent(
            new AggregateCreated(
                $aggregateId,
                Carbon::now()
            )
        );

        return $aggregate;
    }

    /**
     * Fire Event on aggregate & add event to uncommitted.
     *
     * @param \Framekit\Event $event
     * @return void
     */
    public function fireEvent(Event $event): void
    {
        $this->applyChange($event);

        $this->aggregatedEvents[] = $event;
        $this->increaseVersion();
    }

    /**
     * Return uncommitted events.
     *
     * @return array
     */
    public function getUncommittedEvents(): array
    {
        $events = $this->aggregatedEvents;
        $this->aggregatedEvents = [];

        return $events;
    }

    /**
     * Increase version of aggregate.
     *
     * @return void
     */
    public function increaseVersion(): void
    {
        ++$this->aggregateVersion;
    }

    /**
     * Recreate aggregate based on stream of Events.
     *
     * @param string $aggregateId
     * @param array  $events
     * @param bool   $skipEvents
     * @return \Framekit\AggregateRoot
     * @throws \Framekit\Exceptions\MethodUnknown
     */
    public static function recreateFromStream(
        string $aggregateId,
        array $events,
        bool $skipEvents = true
    ): AggregateRoot {
        $aggregate = new static($aggregateId);

        foreach ($events as $e) {

            // check is apply change method exists on aggregate
            if (!$aggregate->understandsEvent($e)) {

                // if method does not exists
                // and recreating can skip events
                // continue loop
                if ($skipEvents) {
                    continue;
                }

                // otherwise throw unknown method exception
                throw new MethodUnknown(
                    sprintf(
                        'Call to undefined apply change method [%s] on aggregate [%s]',
                        $aggregate->composeApplierMethodName($e),
                        get_class($aggregate)
                    )
                );
            }

            $aggregate->applyChange($e);
            $aggregate->increaseVersion();
        }

        return $aggregate;
    }

    /**
     * Boot method is responsible for creating init state.
     *
     * @codeCoverageIgnore
     * @return void
     */
    protected function boot(): void {}

    /**
     * Handle aggregate creation.
     *
     * @param \Framekit\Events\AggregateCreated
     * @return void
     */
    abstract protected function applyAggregateCreated(AggregateCreated $event): void;

    /**
     * Handle aggregate removal.
     *
     * @param \Framekit\Events\AggregateRemoved
     * @return void
     */
    abstract protected function applyAggregateRemoved(AggregateRemoved $event): void;

    /**
     * Is aggregate understands event.
     *
     * @param \Framekit\Event $event
     * @return bool
     */
    abstract protected function understandsEvent(Event $event): bool;

    /**
     * Compose applier method name.
     *
     * @param \Framekit\Event $event
     * @return string
     */
    abstract protected function composeApplierMethodName(Event $event): string;
}
