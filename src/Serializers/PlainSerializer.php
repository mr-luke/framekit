<?php

namespace Mrluke\Framekit\Serializers;

use Mrluke\Framekit\Contracts\Serializable;
use Mrluke\Framekit\Contracts\Serializer;

/**
 * PlainSerializer class serialize event by native PHP
 * function serialize.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class PlainSerializer implements Serializer
{
    /**
     * Serialize object to string.
     *
     * @param  \Mrluke\Framekit\Contracts\Sarializable  $toSerialize
     * @return string
     */
    public function serialize(Sarializable $toSerialize): string
    {
        return serialize($toSerialize);
    }

    /**
     * Unserialize to object.
     *
     * @param  string  $serialized
     * @return \Mrluke\Framekit\Contracts\Serializable
     */
    public function unserialize(string $serialized): Serializable
    {
        return unserialize($serialized);
    }
}
