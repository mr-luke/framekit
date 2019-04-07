<?php

namespace Tests\Unit;

use Tests\UnitCase;
use Tests\NonPublicMethodTool;

use Framekit\Contracts\Bus;
use Framekit\Contracts\Publishable;
use Framekit\Drivers\CommandBus;
use Framekit\Exceptions\MissingHandler;
use Illuminate\Foundation\Application;

/**
 * CommandBus unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class CommandBusTest extends UnitCase
{
    use NonPublicMethodTool;

    public function testClassResolveContract()
    {
        $this->assertInstanceOf(
            Bus::class,
            new CommandBus($this->createMock(Application::class))
        );
    }

    public function testRegisterHandlerViaConstructor()
    {
        $bus = new CommandBus($this->createMock(Application::class), [
            'from' => 'to'
        ]);

        $this->assertEquals(
            ['from' => 'to'],
            $bus->handlers()
        );
    }

    public function testRegisterHandlers()
    {
        $bus = new CommandBus($this->createMock(Application::class));

        $this->assertTrue(!$bus->handlers());

        $bus->register(['from' => 'to']);

        $this->assertEquals(
            ['from' => 'to'],
            $bus->handlers()
        );
    }

    public function testThrowsWhenCommandNotRegistered()
    {
        $this->expectException(MissingHandler::class);

        $bus = new CommandBus($this->createMock(Application::class));

        $compose = self::getMethodOfClass(CommandBus::class, 'getHandler');
        $compose->invokeArgs($bus, ['BadClass']);
    }

    public function testThrowsWhenCommandHasNoHandlerRegistered()
    {
        $this->expectException(MissingHandler::class);

        $bus = new CommandBus($this->createMock(Application::class), ['BadClass' => null]);

        $compose = self::getMethodOfClass(CommandBus::class, 'getHandler');
        $compose->invokeArgs($bus, ['BadClass']);
    }
}
