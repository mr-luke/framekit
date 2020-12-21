<?php

declare(strict_types=1);

namespace Framekit\Contracts;

use Framekit\AggregateRoot;

/**
 * Repository contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
interface Repository
{
    /**
     * Persist changes made on Aggregate.
     *
     * @param  \Framekit\AggregateRoot $aggregate
     * @return mixed
     */
    public function persist(AggregateRoot $aggregate);

    /**
     * Retrieve aggregate by AggregateId.
     *
     * @param  string  $className
     * @param  int|string|\Framekit\Contracts\AggregateIdentifier  $aggregateId
     * @return \Framekit\AggregateRoot
     */
    public function retrieve(string $className, $aggregateId): AggregateRoot;
}
