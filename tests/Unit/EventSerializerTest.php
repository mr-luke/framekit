<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Framekit\Contracts\Serializer;
use Framekit\Eventing\EventSerializer;
use Tests\UnitCase;

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
        $event          = new \Tests\Components\IntegerAdded(1);
        $event->firedAt = 12345678;

        $serializer = new EventSerializer;
        $after      = $serializer->serialize($event);

        $this->assertEquals(
            $this->getQualifiedPlainJson(),
            $after
        );
    }

    public function testUnserializeEventWithoutObject()
    {
        $event          = new \Tests\Components\IntegerAdded(1);
        $event->firedAt = 12345678;

        $serializer = new EventSerializer;
        $class      = $serializer->unserialize($this->getQualifiedPlainJson());

        $this->assertEquals(
            $event,
            $class
        );
    }

    public function testSerializeEventWithObject()
    {
        $now = Carbon::now();

        $event          = new \Tests\Components\DateAdded($now);
        $event->firedAt = 12345678;

        $serializer = new EventSerializer;
        $after      = $serializer->serialize($event);

        $this->assertEquals(
            $this->getQualifiedDateJson($now),
            $after
        );
    }

    public function testUnserializeEventWithObject()
    {
        $now = Carbon::now();

        $event          = new \Tests\Components\DateAdded($now);
        $event->firedAt = 12345678;

        $serializer = new EventSerializer;
        $class      = $serializer->unserialize($this->getQualifiedDateJson($now));

        $this->assertEquals(
            $event,
            $class
        );
    }

    protected function getQualifiedDateJson(Carbon $date): string
    {
        return \json_encode([
            'class'      => 'Tests\Components\DateAdded',
            'attributes' => [
                'date'        => serialize($date),
                'aggregateId' => null,
                'firedAt'     => 12345678,
                '__meta__'    => [],
            ],
        ]);
    }

    protected function getQualifiedPlainJson(): string
    {
        return \json_encode([
            'class'      => 'Tests\Components\IntegerAdded',
            'attributes' => [
                'toAdd'       => 1,
                'aggregateId' => null,
                'firedAt'     => 12345678,
                '__meta__'    => [],
            ],
        ]);
    }
}
