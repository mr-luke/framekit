<?php

namespace Tests\Components;

use Framekit\AggregateRoot;
use Framekit\Extensions\EventSourcedAggregate;

/**
 * Test Event.
 *
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
}
