<?php

namespace Tests\Unit;

use Tests\Components\IntegerAdded;
use Tests\Components\TestAggregate;
use Tests\NonPublicMethodTool;
use Tests\UnitCase;

use Framekit\Contracts\Projector as Contract;
use Framekit\Exceptions\MissingProjection;
use Framekit\Testing\Projector;

/**
 * Testing\Projector unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class TestingProjectorTest extends UnitCase
{
    use NonPublicMethodTool;

    public function testThrowsWhenAggregateNotRegistered()
    {
        $this->expectException(MissingProjection::class);

        $projector = new Projector();
        $projector->project(new TestAggregate('uuid1'), []);
    }

    public function testThrowsWhenAggregateHasNoProjectionRegistered()
    {
        $this->expectException(MissingProjection::class);

        $projector = new Projector([
            TestAggregate::class => null
        ]);
        $projector->project(new TestAggregate('uuid1'), []);
    }

    public function testProjectEvents()
    {
        $projector = new Projector([
            TestAggregate::class => 'DummyProjection'
        ]);
        $projector->project(new TestAggregate('uuid1'), [
            new IntegerAdded(2)
        ]);

        $this->assertEquals(
            [
                TestAggregate::class => ['whenIntegerAdded']
            ],
            $projector->projected()
        );
    }

    public function testIsCalledPossitive()
    {
        $projector = new Projector([
            TestAggregate::class => 'DummyProjection'
        ]);
        $projector->project(new TestAggregate('uuid1'), [
            new IntegerAdded(2)
        ]);

        $compose = self::getMethodOfClass(Projector::class, 'isCalled');

        $this->assertTrue(
            $compose->invokeArgs($projector, [
                TestAggregate::class,
                'DummyProjection',
                'whenIntegerAdded'
            ])
        );
    }

    public function testIsCalledNegative()
    {
        $projector = new Projector([
            'DummyAggregate' => 'DummyProjection'
        ]);

        $compose = self::getMethodOfClass(Projector::class, 'isCalled');

        $this->assertFalse(
            $compose->invokeArgs($projector, [
                'DummyAggregate',
                'DummyProjection',
                'whenIntegerAdded'
            ])
        );
    }
}
