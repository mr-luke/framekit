<?php

declare(strict_types=1);

namespace Framekit\Contracts;

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
     * @param  \Framekit\Contracts\Serializable  $toSerialize
     * @return string
     */
    public function serialize(Serializable $toSerialize): string;

    /**
     * Unserialize to object.
     *
     * @param  string  $serialized
     * @return \Framekit\Contracts\Serializable
     */
    public function unserialize(string $serialized): Serializable;
}
