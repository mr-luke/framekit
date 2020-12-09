<?php

declare(strict_types=1);

namespace Framekit\Extentions;

use Framekit\AggregateRoot;
use Framekit\Event;

/**
 * Entity extension for Aggregate.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 * @parent    \Framekit\AggregateRoot
 *
 * @codeCoverageIgnore
 */
trait EntityAggregate
{
    /**
     * @var array
     */
    protected $aggregatedEvents = [];

    /**
     * Fire Event on aggregate & add event to uncommitted.
     *
     * @param  \Framekit\Event  $event
     * @return void
     */
    public function fireEvent(Event $event): void
    {
        $this->applyChange($event);

        $this->aggregatedEvents[] = $event;
    }

    /**
     * Return unpublished events.
     *
     * @return array
     */
    public function getUnpublishedEvents(): array
    {
        $events = $this->aggregatedEvents;
        $this->aggregatedEvents = [];

        return $events;
    }

    /**
     * Recreate aggregate based on DTO.
     *
     * @param  string  $aggregateId
     * @param  object  $dto
     * @return \Framekit\AggregateRoot
     */
    public static function recreateFromDTO(string $aggregateId, object $dto): AggregateRoot
    {
        $aggregate = new static($aggregateId);
        $aggregate->setState($dto);

        return $aggregate;
    }

    /**
     * Set state for an aggregate.
     *
     * @param  object  $dto
     * @return void
     */
    abstract public function setState(object $dto): void;
}
