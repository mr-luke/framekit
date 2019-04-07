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
 * @license   MIT
 */
interface Repository
{
    /**
     * Persist changes made on Aggregate.
     *
     * @param  \Framekit\AggregateRoot $aggreagate
     * @return void
     */
    public function persist(AggregateRoot $aggreagate): void;

    /**
     * Retrive aggraget by AggregateId.
     *
     * @param  string  $className
     * @param  string  $aggregateId
     * @return \Framekit\AggregateRoot
     */
    public function retrieve(string $className, string $aggregateId): AggregateRoot;
}
