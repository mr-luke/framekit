<?php

namespace Mrluke\Framekit\Contracts;

/**
 * Serializer contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
interface Serializer
{
    /**
     * Serialize object to string.
     *
     * @param  \Mrluke\Framekit\Contracts\Sarializable  $toSerialize
     * @return string
     */
    public function serialize(Sarializable $toSerialize): string;

    /**
     * Unserialize to object.
     *
     * @param  string  $serialized
     * @return \Mrluke\Framekit\Contracts\Serializable
     */
    public function unserialize(string $serialized): Serializable;
}
