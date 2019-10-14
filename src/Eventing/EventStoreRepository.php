<?php

declare(strict_types=1);

namespace Framekit\Eventing;

use ReflectionClass;

use Framekit\AggregateRoot;
use Framekit\Contracts\Bus;
use Framekit\Contracts\EventRepository;
use Framekit\Contracts\Projector;
use Framekit\Contracts\Store;
use Framekit\Exceptions\UnsupportedAggregate;

/**
 * EventStoreRepository class for Framekit.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 * @version   1.0.0
 */
class EventStoreRepository implements EventRepository
{
    /**
     * @var \Framekit\Contracts\Bus
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
     * @param \Framekit\Contracts\Bus       $bus
     * @param \Framekit\Contracts\Store     $store
     * @param \Framekit\Contracts\Projector $projector
     */
    function __construct(Bus $bus, Store $store, Projector $projector)
    {
        $this->eventBus    = $bus;
        $this->eventStore  = $store;
        $this->projector   = $projector;
    }

    /**
     * Persist changes made on Aggregate.
     *
     * @param  \Framekit\AggregateRoot $aggreagate
     * @return void
     */
    public function persist(AggregateRoot $aggreagate): void
    {
        $uncommitedEvents = $aggreagate->getUncommitedEvents();

        $this->eventStore->commitToStream(
            get_class($aggreagate),
            $aggreagate->getId(),
            $uncommitedEvents
        );

        $this->projector->project($aggreagate, $uncommitedEvents);

        foreach ($uncommitedEvents as $e) {
            $this->eventBus->publish($e);
        }
    }

    /**
     * Retrive aggraget by AggregateId.
     *
     * @param  string  $className
     * @param  string  $aggregateId
     * @return \Framekit\AggregateRoot
     */
    public function retrieve(string $className, string $aggregateId): AggregateRoot
    {
        if (! class_exists($className)) {
            throw new UnsupportedAggregate(
                sprintf('Class not found %s', $className)
            );
        }

        $reflection = new ReflectionClass($className);

        if (! $reflection->isInstantiable() || ! $reflection->isSubclassOf(AggregateRoot::class)) {
            throw new UnsupportedAggregate(
                sprintf('Aggregate has to extend %s', AggregateRoot::class)
            );
        }

        $stream = $this->eventStore->loadStream($aggregateId);

        return $className::recreateFromStream($aggregateId, $stream);
    }
}
