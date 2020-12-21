<?php

namespace Tests\Unit;

use Tests\UnitCase;

use Framekit\Contracts\Mapper;
use Framekit\Drivers\EventMapper;
use Illuminate\Foundation\Application;

/**
 * EventMapper unit tests.
 *
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
}
