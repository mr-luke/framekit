<?php

namespace Tests\Unit;

use Tests\UnitCase;

use Carbon\Carbon;
use Framekit\Contracts\Serializer;
use Framekit\Eventing\EventSerializer;
use Framekit\Event;

/**
 * EventSerializer unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class EventSerializerTest extends UnitCase
{
    public function testClassResolveContract()
    {
        $this->assertInstanceOf(
            Serializer::class,
            new EventSerializer
        );
    }

    public function testSerializeEventWithoutObject()
    {
        $serializer = new EventSerializer;
        $after = $serializer->serialize(
            new \Tests\Components\IntegerAdded(1)
        );

        $this->assertEquals(
            $this->getQualifiedPlainJson(),
            $after
        );
    }

    public function testUnserializeEventWithoutObject()
    {
        $serializer = new EventSerializer;
        $class = $serializer->unserialize($this->getQualifiedPlainJson());

        $this->assertEquals(
            new \Tests\Components\IntegerAdded(1),
            $class
        );
    }

    public function testSerializeEventWithObject()
    {
        $now = Carbon::now();
        $serializer = new EventSerializer;
        $after = $serializer->serialize(
            new \Tests\Components\DateAdded($now)
        );

        $this->assertEquals(
            $this->getQualifiedDateJson($now),
            $after
        );
    }

    public function testUnserializeEventWithObject()
    {
        $now = Carbon::now();
        $serializer = new EventSerializer;
        $class = $serializer->unserialize($this->getQualifiedDateJson($now));

        $this->assertEquals(
            new \Tests\Components\DateAdded($now),
            $class
        );
    }

    protected function getQualifiedDateJson(Carbon $date): string
    {
        return \json_encode([
            'class'      => 'Tests\Components\DateAdded',
            'attributes' => [
                'date' => serialize($date),
            ]
        ]);
    }

    protected function getQualifiedPlainJson(): string
    {
        return \json_encode([
            'class'      => 'Tests\Components\IntegerAdded',
            'attributes' => [
                'toAdd' => 1,
            ]
        ]);
    }
}
