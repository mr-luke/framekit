<?php

namespace Tests\Feature;

use Tests\FeatureCase;
use Tests\NonPublicMethodTool;

use Framekit\AggregateRoot;
use Framekit\Drivers\Projector;
use Framekit\Event;
use Framekit\Projection;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/**
 * Projector feature tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class ProjectorTest extends FeatureCase
{
    use NonPublicMethodTool;

    public function testReturnHandler()
    {
        $projector = new Projector(
            $this->createMock(Application::class),
            ['Positive' => 'Tests\Components\DummyProjection']
        );

        $compose = self::getMethodOfClass(Projector::class, 'getProjection');

        $this->assertInstanceOf(
            Projection::class,
            $compose->invokeArgs($projector, ['Positive'])
        );
    }

    public function testThrowWhenItsNotEvent()
    {
        $this->expectException(\InvalidArgumentException::class);

        $aggregateMock = $this->createMock(AggregateRoot::class);

        $projector = new Projector(
            $this->createMock(Application::class),
            [get_class($aggregateMock) => 'Tests\Components\DummyProjection']
        );

        $projector->project($aggregateMock, [$this->createMock(Request::class)]);
    }

    public function testProjectMethod()
    {
        $eventMock = $this->getMockBuilder(Event::class)
                          ->setMethods(['dummy'])
                          ->getMockForAbstractClass();

        $eventMock->expects($this->once())
                  ->method('dummy');

        $aggregateMock = $this->createMock(AggregateRoot::class);

        $projector = new Projector(
            $this->createMock(Application::class),
            [get_class($aggregateMock) => 'Tests\Components\DummyProjection']
        );

        $projector->project($aggregateMock, [$eventMock]);
    }
}
