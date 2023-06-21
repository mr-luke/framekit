<?php

namespace Tests\Unit;

use Framekit\Contracts\Mapper;
use Framekit\Drivers\EventMapper;
use Illuminate\Foundation\Application;
use Tests\Components\DateAdded;
use Tests\Components\DummyReactor;
use Tests\Components\TestMap;
use Tests\UnitCase;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class EventMapperTest extends UnitCase
{
    public function testClassResolveContract()
    {
        $this->assertInstanceOf(
            Mapper::class,
            new EventMapper($this->createMock(Application::class))
        );
    }

    public function testRegisterMapsViaConstructor()
    {
        $bus = new EventMapper($this->createMock(Application::class), [
            'from' => 'to'
        ]);

        $this->assertEquals(
            ['from' => 'to'],
            $bus->mappers()
        );
    }

    public function testRegisterMaps()
    {
        $bus = new EventMapper($this->createMock(Application::class));

        $this->assertTrue(!$bus->mappers());

        $bus->register(['from' => 'to']);

        $this->assertEquals(
            ['from' => 'to'],
            $bus->mappers()
        );
    }

    public function testIfReturnsUntouchedPayloadWhenNoMapAvailable()
    {
        $bus = new EventMapper($this->createMock(Application::class), [
            DateAdded::class => DummyReactor::class
        ]);

        $payload = ['hello' => 'world'];
        $mapped = $bus->map(DateAdded::class, $payload, 1, []);

        $this->assertEquals($payload, $mapped);
    }

    public function testMapEvent()
    {
        $bus = new EventMapper($this->createMock(Application::class), [
            DateAdded::class => TestMap::class
        ]);

        $payload = ['hello' => 'world'];
        $mapped = $bus->map(DateAdded::class, $payload, 1, []);

        $this->assertEquals(['hello' => 'world', 'added' => 1], $mapped);
    }
}
