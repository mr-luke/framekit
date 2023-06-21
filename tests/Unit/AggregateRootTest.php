<?php

namespace Tests\Unit;

use Framekit\AggregateRoot;
use Framekit\Exceptions\InvalidAggregateIdentifier;
use ReflectionClass;
use Tests\Components\IntegerAdded;
use Tests\NonPublicMethodTool;
use Tests\UnitCase;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class AggregateRootTest extends UnitCase
{
    use NonPublicMethodTool;

    public function testUnderstandsEventMethod()
    {
        $event = new IntegerAdded('test', 2);

        $aggreagate = $this->getMockBuilder(AggregateRoot::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['bootRootEntity'])
            ->addMethods(['applyIntegerAdded'])
            ->getMockForAbstractClass();

        $aggreagate->expects($this->never())
            ->method('applyIntegerAdded')
            ->with($this->equalTo($event));

        $aggreagate->understandsEvent($event);
    }

    public function testFireEventMethod()
    {
        $event = new IntegerAdded('test', 2);

        $aggreagate = $this->getMockBuilder(AggregateRoot::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['bootRootEntity'])
            ->addMethods(['applyIntegerAdded'])
            ->getMockForAbstractClass();

        $aggreagate->expects($this->once())
            ->method('applyIntegerAdded')
            ->with($this->equalTo($event));

        $ref = (new ReflectionClass(AggregateRoot::class));
        $method = $ref->getMethod('fireEvent');

        $method->setAccessible(true);
        $method->invokeArgs($aggreagate, [$event]);

        $this->assertEquals([$event], $ref->getProperty('aggregatedEvents')->getValue($aggreagate));
    }

    public function testThrowsWhenIntegerIdIsBelowZero()
    {
        $this->expectException(InvalidAggregateIdentifier::class);

        $this->getMockBuilder(AggregateRoot::class)
            ->setConstructorArgs([-1])
            ->onlyMethods(['bootRootEntity'])
            ->getMockForAbstractClass();
    }

    public function testThrowsWhenStringIdIsNotAnUUID()
    {
        $this->expectException(InvalidAggregateIdentifier::class);

        $this->getMockBuilder(AggregateRoot::class)
            ->setConstructorArgs(['not-uuid'])
            ->onlyMethods(['bootRootEntity'])
            ->getMockForAbstractClass();
    }
}
