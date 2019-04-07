<?php

namespace Tests\Unit;

use Tests\UnitCase;

use Carbon\Carbon;
use Framekit\Contracts\Serializable;
use Framekit\State;

/**
 * State unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class StateTest extends UnitCase
{
    public function testClassResolveContract()
    {
        $state = $this->getMockForAbstractClass(State::class, ['uuid', Carbon::now()]);

        $this->assertInstanceOf(
            Serializable::class,
            $state
        );
    }

    public function testGetCreatedAt()
    {
        $date = Carbon::now();
        $state = $this->getMockForAbstractClass(State::class, ['uuid', $date]);

        $this->assertEquals(
            $date,
            $state->getCreatedAt()
        );

        $this->assertEquals(
            null,
            $state->getDeletedAt()
        );
    }

    public function testMarkAsRemoved()
    {
        $date = Carbon::now();
        $state = $this->getMockForAbstractClass(State::class, ['uuid', $date]);
        $state->markAsRemoved($date->addDay());

        $this->assertEquals(
            $date,
            $state->getDeletedAt()
        );
    }

    public function testInitState()
    {
        $date = Carbon::now();
        $state = \Tests\Components\SumState::init('uuid');

        $this->assertEquals(
            $date->format('Y-m-d H:i'),
            $state->getCreatedAt()->format('Y-m-d H:i')
        );

        $this->assertEquals(
            'uuid',
            $state->id
        );
    }
}
