<?php

namespace Tests\Unit;

use Framekit\Exceptions\MissingProjection;
use Framekit\Testing\Projector;
use Illuminate\Support\Str;
use Tests\Components\IntegerAdded;
use Tests\Components\TestAggregate;
use Tests\NonPublicMethodTool;
use Tests\UnitCase;

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

        $uuid = Str::uuid();
        $projector = new Projector();
        $projector->projectByEvents(new TestAggregate($uuid), []);
    }

    public function testThrowsWhenAggregateHasNoProjectionRegistered()
    {
        $this->expectException(MissingProjection::class);

        $uuid = Str::uuid();
        $projector = new Projector([
            TestAggregate::class => null
        ]);
        $projector->project(new TestAggregate($uuid));
    }

    public function testProjectEvents()
    {
        $projector = new Projector([
            TestAggregate::class => 'DummyProjection'
        ]);

        $uuid = Str::uuid();
        $projector->projectByEvents(new TestAggregate($uuid), [
            new IntegerAdded($uuid, 2)
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

        $uuid = Str::uuid();
        $projector->projectByEvents(new TestAggregate($uuid), [
            new IntegerAdded($uuid, 2)
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
