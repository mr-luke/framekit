<?php

namespace Tests\Components;

use Carbon\Carbon;
use Framekit\AggregateRoot;
use Framekit\Events\AggregateCreated;
use Framekit\Events\AggregateRemoved;
use Framekit\Extentions\EventSourcedAggregate;

/**
 * Test Event.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class TestAggregate extends AggregateRoot
{
    use EventSourcedAggregate;

    /**
     * Handle aggreagate creation.
     *
     * @param  \Framekit\Events\AggregateCreated
     * @return void
     */
    protected function applyAggregateCreated(AggregateCreated $event): void
    {

    }

    /**
     * Handle aggreagate removal.
     *
     * @param  \Framekit\Events\AggregateRemoved
     * @return void
     */
    protected function applyAggregateRemoved(AggregateRemoved $event): void
    {

    }
}
