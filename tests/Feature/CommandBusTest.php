<?php

namespace Tests\Feature;

use Tests\FeatureCase;
use Tests\Components\ResolveTest;
use Tests\NonPublicMethodTool;

use Framekit\Contracts\Command;
use Framekit\Drivers\CommandBus;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/**
 * CommandBus feature tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class CommandBusTest extends FeatureCase
{
    use NonPublicMethodTool;

    public function testReturnHandler()
    {
        $return = $this->createMock(Request::class);

        $appMock = $this->getMockBuilder(Application::class)
                        ->setMethods(['make'])
                        ->getMock();

        $appMock->expects($this->once())
                ->method('make')
                ->with($this->equalTo('Illuminate\Http\Request'))
                ->willReturn($return);

        $bus = new CommandBus($appMock, ['Positive' => 'Tests\Components\ResolveTest']);

        $compose = self::getMethodOfClass(CommandBus::class, 'getHandler');

        $this->assertInstanceOf(
            ResolveTest::class,
            $compose->invokeArgs($bus, ['Positive'])
        );
    }

    public function testPublishMethod()
    {
        $command = $this->createMock(Command::class);
        $return  = $this->createMock(Request::class);

        $appMock = $this->getMockBuilder(Application::class)
                        ->setMethods(['make'])
                        ->getMock();

        $appMock->expects($this->once())
                ->method('make')
                ->with($this->equalTo('Illuminate\Http\Request'))
                ->willReturn($return);

        $bus = new CommandBus($appMock, [get_class($command) => 'Tests\Components\ResolveTest']);
        $bus->publish($command);
    }
}
