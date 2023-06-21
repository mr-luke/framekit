<?php

declare(strict_types=1);

namespace Framekit\Contracts;

use Framekit\AggregateRoot;

/**
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
     * @param \Framekit\AggregateRoot $aggregate
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Mrluke\Bus\Exceptions\InvalidAction
     * @throws \Mrluke\Bus\Exceptions\InvalidHandler
     * @throws \Mrluke\Bus\Exceptions\MissingConfiguration
     * @throws \Mrluke\Bus\Exceptions\MissingHandler
     * @throws \Mrluke\Bus\Exceptions\MissingProcess
     * @throws \Mrluke\Bus\Exceptions\RuntimeException
     * @throws \ReflectionException
     */
    public function persist(AggregateRoot $aggregate): void;

    /**
     * Retrieve aggregate by AggregateId.
     *
     * @param string                                             $className
     * @param int|string|\Framekit\Contracts\AggregateIdentifier $aggregateId
     * @return \Framekit\AggregateRoot
     * @throws \Framekit\Exceptions\InvalidAggregateIdentifier
     * @throws \Framekit\Exceptions\StreamNotFound
     * @throws \ReflectionException
     */
    public function retrieve(
        string                         $className,
        int|string|AggregateIdentifier $aggregateId
    ): AggregateRoot;
}
