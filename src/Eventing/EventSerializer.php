<?php

declare(strict_types=1);

namespace Framekit\Eventing;

use ReflectionClass;
use ReflectionProperty;

use Framekit\Contracts\Serializable;
use Framekit\Contracts\Serializer;

final class EventSerializer implements Serializer
{
    /**
     * Serialize object to string.
     *
     * @param  \Framekit\Contracts\Sarializable  $toSerialize
     * @param  string                                   $subject
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function serialize(Serializable $toSerialize): string
    {
        $attributes   = [];
        $reflection   = new ReflectionClass($toSerialize);
        $propertities = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($propertities as $p) {
            if ($p->isStatic()) continue;
            $value = $p->getValue($toSerialize);

            if (is_object($value)) {
                $value = serialize($value);
            }

            $attributes[$p->getName()] = $value;
        }

        return json_encode([
            'class'      => get_class($toSerialize),
            'attributes' => $attributes
        ]);
    }

    /**
     * Unserialize to object.
     *
     * @param  string  $serialized
     * @param  string  $subject
     * @return \Framekit\Contracts\Serializable
     *
     * @throws \InvalidArgumentException
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

        return $instance;
    }
}
