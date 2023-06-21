<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Framekit\Eventing\EventSerializer;
use Tests\Components\DateAdded;
use Tests\Components\IntegerAdded;
use Tests\UnitCase;
use function json_encode;

/**
 * EventSerializer unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class EventSerializerTest extends UnitCase
{
    public function testSerializeEventWithoutObject()
    {
        $event = new IntegerAdded('test', 1);
        $event->firedAt = 12345678;

        $serializer = new EventSerializer;
        $after = $serializer->serialize($event);

        $this->assertEquals(
            $this->getQualifiedPlainJson(),
            $after
        );
    }

    public function testUnserializeEventWithoutObject()
    {
        $event = new IntegerAdded('test', 1);
        $event->firedAt = 12345678;

        $serializer = new EventSerializer;
        $class = $serializer->unserialize($this->getQualifiedPlainJson());

        $this->assertEquals(
            $event,
            $class
        );
    }

    public function testSerializeEventWithObject()
    {
        $now = Carbon::now();

        $event = new DateAdded('test', $now);
        $event->firedAt = 12345678;

        $serializer = new EventSerializer;
        $after = $serializer->serialize($event);

        $this->assertEquals(
            $this->getQualifiedDateJson($now),
            $after
        );
    }

    public function testUnserializeEventWithObject()
    {
        $now = Carbon::now();

        $event = new DateAdded('test', $now);
        $event->firedAt = 12345678;

        $serializer = new EventSerializer;
        $class = $serializer->unserialize($this->getQualifiedDateJson($now));

        $this->assertEquals(
            $event,
            $class
        );
    }

    protected function getQualifiedDateJson(Carbon $date): string
    {
        return json_encode([
            'class' => 'Tests\Components\DateAdded',
            'attributes' => [
                '__meta__' => [],
                'aggregateId' => 'test',
                'date' => serialize($date),
                'firedAt' => 12345678,
            ],
        ]);
    }

    protected function getQualifiedPlainJson(): string
    {
        return json_encode([
            'class' => 'Tests\Components\IntegerAdded',
            'attributes' => [
                '__meta__' => [],
                'aggregateId' => 'test',
                'firedAt' => 12345678,
                'toAdd' => 1,
            ],
        ]);
    }
}
