<?php

declare(strict_types=1);

namespace Framekit\Eventing;

use Framekit\Contracts\Serializable;
use Framekit\Contracts\Serializer;
use ReflectionClass;
use ReflectionProperty;

/**
 * EventSerializer class serialize events of Aggregate
 * for stream persistence.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
final class EventSerializer implements Serializer
{
    /**
     * Serialize object to string.
     *
     * @param \Framekit\Contracts\Serializable $toSerialize
     * @return string
     */
    public function serialize(Serializable $toSerialize): string
    {
        $attributes = [];
        $reflection = new ReflectionClass($toSerialize);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $p) {
            if ($p->isStatic()) continue;
            $value = $p->getValue($toSerialize);

            if (is_object($value)) {
                $value = serialize($value);
            }

            $attributes[$p->getName()] = $value;
        }

        ksort($attributes);

        return json_encode(
            [
                'class' => get_class($toSerialize),
                'attributes' => $attributes
            ]
        );
    }

    /**
     * Unserialize to object.
     *
     * @param string $serialized
     * @return \Framekit\Contracts\Serializable
     * @throws \ReflectionException
     */
    public function unserialize(string $serialized): Serializable
    {
        $payload = json_decode($serialized, true);

        $reflection = new ReflectionClass($payload['class']);
        $instance = $reflection->newInstanceWithoutConstructor();

        foreach ($payload['attributes'] as $a => $v) {
            if (@unserialize((string)$v) !== false) {
                $v = unserialize($v);
            }
            $instance->{$a} = $v;
        }

        /* @var Serializable $instance */
        return $instance;
    }
}
