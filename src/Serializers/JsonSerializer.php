<?php

namespace Mrluke\Framekit\Serializers;

use Mrluke\Framekit\Contracts\Serializable;
use Mrluke\Framekit\Contracts\Serializer;

/**
 * JsonSerializer class serialize Event as json encoded string.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class JsonSerializer implements Serializer
{
    /**
     * Serialize object to string.
     *
     * @param  \Mrluke\Framekit\Contracts\Sarializable  $toSerialize
     * @return string
     */
    public function serialize(Sarializable $toSerialize): string
    {
        // TODO!
        //return serialize($toSerialize);
    }

    /**
     * Unserialize to object.
     *
     * @param  string  $serialized
     * @return \Mrluke\Framekit\Contracts\Serializable
     */
    public function unserialize(string $serialized): Serializable
    {
        // TODO!
        //return unserialize($serialized);
    }
}
