<?php

declare(strict_types=1);

namespace Framekit\Eventing;

use Framekit\Contracts\AggregateIdentifier;
use Framekit\Extensions\ValidatesUuid;
use ReflectionClass;

use Framekit\AggregateRoot;
use Framekit\Contracts\EventBus;
use Framekit\Contracts\EventRepository;
use Framekit\Contracts\Projector;
use Framekit\Contracts\Store;
use Framekit\Exceptions\InvalidAggregateIdentifier;

/**
 * EventStoreRepository class for Framekit.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 * @version   1.0.0
 */
class EventStoreRepository implements EventRepository
{
    use ValidatesUuid;

    /**
     * @var \Framekit\Contracts\EventBus
     */
    protected $eventBus;

    /**
     * @var \Framekit\Contracts\Store
     */
    protected $eventStore;

    /**
     * @var \Framekit\Contracts\Projector
     */
    protected $projector;

    /**
     * @param \Framekit\Contracts\EventBus  $bus
     * @param \Framekit\Contracts\Store     $store
     * @param \Framekit\Contracts\Projector $projector
     */
    function __construct(EventBus $bus, Store $store, Projector $projector)
    {
        $this->eventBus = $bus;
        $this->eventStore = $store;
        $this->projector = $projector;
    }

    /**
     * Persist changes made on Aggregate.
     *
     * @param \Framekit\AggregateRoot $aggregate
     * @return void
     */
    public function persist(AggregateRoot $aggregate): void
    {
        $uncommittedEvents = $aggregate->unpublishedEvents();

        $this->eventStore->commitToStream(
            get_class($aggregate),
            $aggregate->identifier(),
            $uncommittedEvents
        );

        $this->projector->project($aggregate, $uncommittedEvents);

        foreach ($uncommittedEvents as $e) {
            $this->eventBus->publish($e);
        }
    }

    /**
     * Retrieve aggregate by AggregateId.
     *
     * @param string                                             $className
     * @param int|string|\Framekit\Contracts\AggregateIdentifier $aggregateId
     * @return \Framekit\AggregateRoot
     * @throws \Framekit\Exceptions\InvalidAggregateIdentifier|\ReflectionException
     */
    public function retrieve(string $className, $aggregateId): AggregateRoot
    {
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
