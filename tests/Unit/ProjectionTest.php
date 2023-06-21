<?php

namespace Tests\Unit;

use Framekit\Exceptions\MethodUnknown;
use Framekit\Projection;
use ReflectionClass;
use Tests\Components\IntegerAdded;
use Tests\UnitCase;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class ProjectionTest extends UnitCase
{
    public function testThrowWhenMagicCall()
    {
        $this->expectException(MethodUnknown::class);

        $projection = $this->getMockForAbstractClass(Projection::class);

        $property = (new ReflectionClass($projection))->getProperty('ignoreUnknownEvents');
        $property->setValue($projection, false);

        $projection->testNotExisting();
    }

    public function testHandleMethod()
    {
        $event = new IntegerAdded('test', 2);
        $projection = $this->getMockBuilder(Projection::class)
                           ->setMethods(['whenIntegerAdded'])
                           ->getMockForAbstractClass();

        $projection->expects($this->once())
                   ->method('whenIntegerAdded')
                   ->with($this->equalTo($event));

        $projection->handle($event);
    }
}
