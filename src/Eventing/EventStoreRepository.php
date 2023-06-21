<?php

declare(strict_types=1);

namespace Framekit\Eventing;

use Framekit\AggregateRoot;
use Framekit\Contracts\AggregateIdentifier;
use Framekit\Contracts\EventBus;
use Framekit\Contracts\EventRepository;
use Framekit\Contracts\Projector;
use Framekit\Contracts\Store;
use Framekit\Exceptions\InvalidAggregateIdentifier;
use Framekit\Extensions\ValidatesUuid;
use ReflectionClass;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class EventStoreRepository implements EventRepository
{
    use ValidatesUuid;

    /**
     * @var \Framekit\Contracts\EventBus
     */
    protected EventBus $eventBus;

    /**
     * @var \Framekit\Contracts\Store
     */
    protected Store $eventStore;

    /**
     * @var \Framekit\Contracts\Projector
     */
    protected Projector $projector;

    /**
     * @param \Framekit\Contracts\EventBus  $bus
     * @param \Framekit\Contracts\Store     $store
     * @param \Framekit\Contracts\Projector $projector
     */
    public function __construct(EventBus $bus, Store $store, Projector $projector)
    {
        $this->eventBus = $bus;
        $this->eventStore = $store;
        $this->projector = $projector;
    }

    /**
     * @inheritDoc
     */
    public function persist(AggregateRoot $aggregate): void
    {
        $uncommittedEvents = $aggregate->unpublishedEvents();

        $this->eventStore->commitToStream(
            get_class($aggregate),
            $aggregate->identifier(),
            $uncommittedEvents
        );

        $this->projector->projectByEvents($aggregate, $uncommittedEvents);

        foreach ($uncommittedEvents as $e) {
            $this->eventBus->publish($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function retrieve(
        string                         $className,
        int|string|AggregateIdentifier $aggregateId
    ): AggregateRoot {
        if (!class_exists($className)) {
            throw new InvalidAggregateIdentifier(
                sprintf('Class not found %s', $className)
            );
        }

        $identifier = $aggregateId instanceof AggregateIdentifier
            ? $aggregateId->toString() : $aggregateId;

        if (!is_string($identifier) || !$this->isValidUuid($identifier)) {
            throw new InvalidAggregateIdentifier(
                'EventStore requires uuid as an aggregate identifier'
            );
        }

        $reflection = new ReflectionClass($className);

        if (!$reflection->isInstantiable() || !$reflection->isSubclassOf(AggregateRoot::class)) {
            throw new InvalidAggregateIdentifier(
                sprintf('Aggregate has to extend %s', AggregateRoot::class)
            );
        }

        return $className::recreateFromStream(
            $aggregateId,
            $this->eventStore->loadStream($identifier)
        );
    }
}
