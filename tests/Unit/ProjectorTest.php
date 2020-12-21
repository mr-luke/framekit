<?php

namespace Tests\Unit;

use Tests\UnitCase;
use Tests\NonPublicMethodTool;

use Framekit\Contracts\Projector as Contract;
use Framekit\Contracts\Publishable;
use Framekit\Drivers\Projector;
use Framekit\Exceptions\MethodUnknown;
use Framekit\Exceptions\MissingProjection;
use Illuminate\Foundation\Application;

/**
 * Projector unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
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

    public function testThrowsWhenAggregateNotRegistered()
    {
        $this->expectException(MissingProjection::class);

        $projector = new Projector($this->createMock(Application::class));

        $compose = self::getMethodOfClass(Projector::class, 'getProjection');
        $compose->invokeArgs($projector, ['BadClass']);
    }

    public function testThrowsWhenAggregateHasNoProjectionRegistered()
    {
        $this->expectException(MissingProjection::class);

        $projector = new Projector($this->createMock(Application::class), ['BadClass' => null]);

        $compose = self::getMethodOfClass(Projector::class, 'getProjection');
        $compose->invokeArgs($projector, ['BadClass']);
    }

    public function testThrowsWhenCallingAssertForProd()
    {
        $this->expectException(MethodUnknown::class);

        $projector = new Projector($this->createMock(Application::class));
        $projector->badMethod();
    }
}
