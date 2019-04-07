<?php

namespace Tests\Unit;

use Tests\UnitCase;

use Framekit\Projection;
use Framekit\Exceptions\MethodUnknown;

/**
 * Projection unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class ProjectionTest extends UnitCase
{
    public function testThrowWhenMagicCall()
    {
        $this->expectException(MethodUnknown::class);

        $projection = $this->getMockForAbstractClass(Projection::class);
        $projection->testNotExisting();
    }

    public function testHandleMethod()
    {
        $event = new \Tests\Components\IntegerAdded(2);
        $projection = $this->getMockBuilder(Projection::class)
                           ->setMethods(['whenIntegerAdded'])
                           ->getMockForAbstractClass();

        $projection->expects($this->once())
                   ->method('whenIntegerAdded')
                   ->with($this->equalTo($event));

        $projection->handle($event);
    }
}
