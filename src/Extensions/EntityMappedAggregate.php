<?php

declare(strict_types=1);

namespace Framekit\Extensions;

use Framekit\AggregateRoot;
use Framekit\Contracts\AggregateIdentifier;
use Framekit\Contracts\DataTransferObject;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 * @parent    \Framekit\AggregateRoot
 * @codeCoverageIgnore
 */
trait EntityMappedAggregate
{
    /**
     * Recreate aggregate based on DTO.
     *
     * @param int|string|\Framekit\Contracts\AggregateIdentifier $aggregateId
     * @param \Framekit\Contracts\DataTransferObject             $dto
     * @return \Framekit\AggregateRoot
     * @throws \Framekit\Exceptions\InvalidAggregateIdentifier
     */
    public static function recreateFromTransferObject(
        int|string|AggregateIdentifier $aggregateId,
        DataTransferObject             $dto
    ): AggregateRoot {
        $aggregate = new static($aggregateId);
        $aggregate->setRoot($dto);

        /* @var AggregateRoot $aggregate */
        return $aggregate;
    }

    /**
     * Set state for an aggregate.
     *
     * @param \Framekit\Contracts\DataTransferObject $dto
     * @return void
     */
    abstract public function setRoot(DataTransferObject $dto): void;
}
