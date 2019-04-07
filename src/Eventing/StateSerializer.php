<?php

namespace Framekit\Eventing;

use Framekit\Contracts\Serializable;
use Framekit\Contracts\Serializer;

/**
 * StateSerializer class serialize state of Aggregate
 * for Snapshoting.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
final class StateSerializer implements Serializer
{
    /**
     * Serialize object to string.
     *
     * @param  \Framekit\Contracts\Serializable  $toSerialize
     * @return string
     */
    public function serialize(Serializable $toSerialize): string
    {
        return serialize($toSerialize);
    }

    /**
     * Unserialize to object.
     *
     * @param  string  $serialized
     * @return \Framekit\Contracts\Serializable
     */
    public function unserialize(string $serialized): Serializable
    {
        return unserialize($serialized);
    }
}
