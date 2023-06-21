<?php

declare(strict_types=1);

namespace Framekit;

use Framekit\Contracts\AggregateIdentifier;
use Framekit\Contracts\Projectable;
use Framekit\Exceptions\InvalidAggregateIdentifier;
use Framekit\Exceptions\MethodUnknown;
use Framekit\Extensions\ValidatesUuid;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
abstract class AggregateRoot implements Projectable
{
    use ValidatesUuid;

    /**
     * @var int|string|\Framekit\Contracts\AggregateIdentifier
     */
    protected AggregateIdentifier|string|int $aggregateId;

    /**
     * @var \Framekit\Event[]
     */
    protected array $aggregatedEvents = [];

    /**
     * @var \Framekit\Entity
     */
    protected Entity $rootEntity;

    /**
     * @param \Framekit\Contracts\AggregateIdentifier|string|int $aggregateId
     * @throws \Framekit\Exceptions\InvalidAggregateIdentifier
     */
    public function __construct(AggregateIdentifier|string|int $aggregateId)
    {
        $this->aggregateId = $aggregateId;
        $this->validateAggregateIdentifier();

        $this->bootRootEntity();
    }

    /**
     * Capture all bad calls.
     *
     * @param string $name
     * @param array  $arguments
     * @throws \Framekit\Exceptions\MethodUnknown
     * @codeCoverageIgnore
     */
    public function __call(string $name, array $arguments)
    {
        throw new MethodUnknown(
            sprintf('Trying to call unknown method [%s]', $name)
        );
    }

    /**
     * Capture all bad calls.
     *
     * @param string $name
     * @param array  $arguments
     * @throws \Framekit\Exceptions\MethodUnknown
     * @codeCoverageIgnore
     */
    public static function __callStatic(string $name, array $arguments)
    {
        throw new MethodUnknown(
            sprintf('Trying to call unknown method [%s]', $name)
        );
    }

    /**
     * Return aggregate identifier.
     *
     * @return int|string|\Framekit\Contracts\AggregateIdentifier
     * @codeCoverageIgnore
     */
    public function identifier(): int|string|AggregateIdentifier
    {
        return $this->aggregateId;
    }

    /**
     * Return root Entity of an aggregate.
     *
     * @return \Framekit\Entity|null
     * @codeCoverageIgnore
     */
    public function rootEntity(): ?Entity
    {
        return $this->rootEntity;
    }

    /**
     * Determine is apply change method exists.
     *
     * @param \Framekit\Event $event
     * @return bool
     */
    public function understandsEvent(Event $event): bool
    {
        return method_exists(
            $this,
            $this->composeApplierMethodName($event)
        );
    }

    /**
     * Return unpublished events.
     *
     * @return \Framekit\Event[]
     * @codeCoverageIgnore
     */
    public function unpublishedEvents(): array
    {
        $events = $this->aggregatedEvents;
        $this->aggregatedEvents = [];

        return $events;
    }

    /**
     * Apply new Event on aggregate state.
     *
     * @param \Framekit\Event $event
     * @return void
     */
    protected function applyChange(Event $event): void
    {
        $eventApplierMethod = $this->composeApplierMethodName($event);

        $this->{$eventApplierMethod}($event);
    }

    /**
     * Boot method is responsible for initiate root Entity.
     *
     * @return void
     */
    abstract protected function bootRootEntity(): void;

    /**
     * Compose apply change method name.
     *
     * @param \Framekit\Event $event
     * @return string
     */
    protected function composeApplierMethodName(Event $event): string
    {
        $classNameParts = explode('\\', get_class($event));
        $eventName = end($classNameParts);

        return "apply{$eventName}";
    }

    /**
     * Fire Event on aggregate & add event to uncommitted.
     *
     * @param \Framekit\Event $event
     * @return void
     */
    protected function fireEvent(Event $event): void
    {
        $this->applyChange($event);

        $this->aggregatedEvents[] = $event;
    }

    /**
     * Validate if Aggregate has correct identifier applied.
     *
     * @return void
     * @throws \Framekit\Exceptions\InvalidAggregateIdentifier
     */
    protected function validateAggregateIdentifier(): void
    {
        if (
            is_int($this->aggregateId) &&
            $this->aggregateId < 1
        ) {
            throw new InvalidAggregateIdentifier(
                'Numeric identifier must be unsigned integer'
            );
        }

        if (
            is_string($this->aggregateId) &&
            !$this->isValidUuid($this->aggregateId)
        ) {
            throw new InvalidAggregateIdentifier(
                'String identifier must be an UUID'
            );
        }
    }
}
