<?php

declare(strict_types=1);

namespace Framekit\Extensions;

use Framekit\AggregateRoot;
use Framekit\Contracts\AggregateIdentifier;
use Framekit\Event;
use Framekit\Exceptions\MethodUnknown;

/**
 * Event Sourcing extension for Aggregate.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
trait EventSourcedAggregate
{
    /**
     * @var int
     */
    protected int $aggregateVersion = 0;

    /**
     * Process stream of events.
     *
     * @param array $events
     * @param bool  $skipUnknown
     * @throws \Framekit\Exceptions\MethodUnknown
     */
    public function processEventsStream(array $events, bool $skipUnknown): void
    {
        foreach ($events as $e) {

            // check is apply change method exists on aggregate
            if (!$this->understandsEvent($e)) {

                // if method does not exist
                // and recreating can skip events
                // continue loop
                if ($skipUnknown) {
                    continue;
                }

                // otherwise throw unknown method exception
                throw new MethodUnknown(
                    sprintf(
                        'Call to undefined apply change method [%s] on aggregate [%s]',
                        $this->composeApplierMethodName($e),
                        get_class($this)
                    )
                );
            }

            $this->applyChange($e);
            $this->increaseVersion();
        }
    }

    /**
     * Recreate aggregate based on stream of Events.
     *
     * @param int|string|\Framekit\Contracts\AggregateIdentifier $aggregateId
     * @param \Framekit\Event[]                                  $events
     * @param bool                                               $skipEvents
     * @return \Framekit\AggregateRoot
     * @throws \Framekit\Exceptions\MethodUnknown
     */
    public static function recreateFromStream(
        int|string|AggregateIdentifier $aggregateId,
        array                          $events,
        bool                           $skipEvents = true
    ): AggregateRoot {
        $aggregate = new static($aggregateId);
        $aggregate->processEventsStream($events, $skipEvents);

        /* @var AggregateRoot $aggregate */
        return $aggregate;
    }

    /**
     * Return actual version of an Aggregate
     *
     * @return int
     */
    public function version(): int
    {
        return $this->aggregateVersion;
    }

    /**
     * Fire Event on aggregate & add event to uncommitted.
     *
     * @param \Framekit\Event $event
     * @return void
     */
    protected function fireEvent(Event $event): void
    {
        parent::fireEvent($event);

        $this->increaseVersion();
    }

    /**
     * Increase version of aggregate.
     *
     * @return void
     */
    protected function increaseVersion(): void
    {
        ++$this->aggregateVersion;
    }
}
