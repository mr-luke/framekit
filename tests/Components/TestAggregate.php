<?php

namespace Tests\Components;

use Framekit\AggregateRoot;
use Framekit\Event;
use Framekit\Extensions\EventSourcedAggregate;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class TestAggregate extends AggregateRoot
{
    use EventSourcedAggregate;

    /**
     * @inheritDoc
     */
    protected function bootRootEntity(): void
    {
        // TODO: Implement bootRootEntity() method.
    }

    public function do(Event $event): void
    {
        $this->fireEvent($event);
    }
}
