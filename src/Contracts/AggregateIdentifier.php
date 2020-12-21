<?php

namespace Framekit\Contracts;

/**
 * Interface AggregateIdentifier
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
interface AggregateIdentifier
{
    /**
     * Determine of given AggregateIdentifier is same as given.
     *
     * @param \Framekit\Contracts\AggregateIdentifier $toCompare
     * @return bool
     */
    public function isEqualTo(AggregateIdentifier $toCompare): bool;

    /**
     * Return string equivalent of identifier.
     *
     * @return string
     */
    public function toString(): string;
}
