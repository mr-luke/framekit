<?php

declare(strict_types=1);

namespace Framekit\Extentions;

use Carbon\Carbon;
use Framekit\AggregateRoot;
use Framekit\Event;
use Framekit\Events\AggregateCreated;
use Framekit\Events\AggregateRemoved;

/**
 * Event Sourcing extention for Aggregate.
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
    protected $aggreagateEvents = [];

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
     * @param  string  $aggregateId
     * @return \Framekit\AggregateRoot
     */
    public static function create(string $aggregateId): AggregateRoot
    {
        $aggregate = new static($aggregateId);
        $aggregate->fireEvent(new AggregateCreated(
            $aggregateId,
            Carbon::now()
        ));

        return $aggregate;
    }

    /**
     * Fire Event on aggregate & add event to uncommited.
     *
     * @param  \Framekit\Event  $event
     * @return void
     */
    public function fireEvent(Event $event): void
    {
        $this->applyChange($event);

        $this->aggreagateEvents[] = $event;
        $this->increaseVersion();
    }

    /**
     * Return uncommited events.
     *
     * @return array
     */
    public function getUncommitedEvents(): array
    {
        $events = $this->aggreagateEvents;
        $this->aggreagateEvents = [];

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
     * @param  string  $aggregateId
     * @param  array   $events
     * @return \Framekit\AggregateRoot
     */
    public static function recreateFromStream(string $aggregateId, array $events): AggregateRoot
    {
        $aggregate = new static($aggregateId);

        foreach ($events as $e) {
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
     * Handle aggreagate creation.
     *
     * @param  \Framekit\Events\AggregateCreated
     * @return void
     */
    abstract protected function applyAggregateCreated(AggregateCreated $event): void;

    /**
     * Handle aggreagate removal.
     *
     * @param  \Framekit\Events\AggregateRemoved
     * @return void
     */
    abstract protected function applyAggregateRemoved(AggregateRemoved $event): void;
}
