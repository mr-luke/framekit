<?php

namespace Tests\Unit;

use Tests\UnitCase;
use Tests\NonPublicMethodTool;

use Framekit\Contracts\Projector as Contract;
use Framekit\Contracts\Publishable;
use Framekit\Drivers\Projector;
use Framekit\Exceptions\MissingProjection;
use Illuminate\Foundation\Application;

/**
 * Projector unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class ProjectorTest extends UnitCase
{
    use NonPublicMethodTool;

    public function testClassResolveContract()
    {
        $this->assertInstanceOf(
            Contract::class,
            new Projector($this->createMock(Application::class))
        );
    }

    public function testRegisterHandlerViaConstructor()
    {
        $projector = new Projector($this->createMock(Application::class), [
            'from' => 'to'
        ]);

        $this->assertEquals(
            ['from' => 'to'],
            $projector->projections()
        );
    }

    public function testRegisterHandlers()
    {
        $projector = new Projector($this->createMock(Application::class));

        $this->assertTrue(!$projector->projections());

        $projector->register(['from' => 'to']);

        $this->assertEquals(
            ['from' => 'to'],
            $projector->projections()
        );
    }

    public function testThrowsWhenCommandNotRegistered()
    {
        $this->expectException(MissingProjection::class);

        $projector = new Projector($this->createMock(Application::class));

        $compose = self::getMethodOfClass(Projector::class, 'getProjection');
        $compose->invokeArgs($projector, ['BadClass']);
    }

    public function testThrowsWhenCommandHasNoHandlerRegistered()
    {
        $this->expectException(MissingProjection::class);

        $projector = new Projector($this->createMock(Application::class), ['BadClass' => null]);

        $compose = self::getMethodOfClass(Projector::class, 'getProjection');
        $compose->invokeArgs($projector, ['BadClass']);
    }
}
