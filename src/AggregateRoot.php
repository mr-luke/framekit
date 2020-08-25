<?php

declare(strict_types=1);

namespace Framekit;

use Framekit\Exceptions\MethodUnknown;

/**
 * Aggregate abstract class.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
abstract class AggregateRoot
{
    /**
     * @var string (uuid)
     */
    protected $aggregateId;

    /**
     * @var \Framekit\State
     */
    protected $state;

    public function __construct(string $aggregateId)
    {
        $this->aggregateId = $aggregateId;

        $this->boot();
    }

    /**
     * Return aggregate identifier.
     *
     * @return string
     */
    public function getId()
    {
        return $this->aggregateId;
    }

    /**
     * Return state of aggregate.
     *
     * @return \Framekit\State
     */
    public function getState(): ?State
    {
        return $this->state;
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
            sprintf('Trying to call unknown method [%s]', $name)
        );
    }

    /**
     * Capture all bad calls.
     *
     * @param  string $name
     * @param  array  $arguments
     * @return \Framekit\Exceptions\MethodUnknown
     */
    public static function __callStatic(string $name, array $arguments)
    {
        throw new MethodUnknown(
            sprintf('Trying to call unknown method [%s]', $name)
        );
    }

    /**
     * Apply new Event on aggregate state.
     *
     * @param  \Framekit\Event $event
     * @return void
     */
    protected function applyChange(Event $event): void
    {
        $eventApplierMethod = $this->composeApplierMethodName($event);

        $this->{$eventApplierMethod}($event);
    }

    /**
     * Compose apply change method name.
     *
     * @param \Framekit\Event $event
     * @return string
     */
    protected function composeApplierMethodName(Event $event): string
    {
        $classNameParts = explode('\\', get_class($event));
        $eventName      = end($classNameParts);

        return "apply{$eventName}";
    }

    /**
     * Determine is apply change method exists.
     *
     * @param \Framekit\Event $event
     * @return bool
     */
    protected function understandsEvent(Event $event): bool
    {
        return method_exists(
            $this,
            $this->composeApplierMethodName($event)
        );
    }

    /**
     * Boot method is responsible for creating init state.
     *
     * @return void
     */
    abstract protected function boot(): void;
}
