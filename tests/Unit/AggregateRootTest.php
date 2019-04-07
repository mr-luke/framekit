<?php

namespace Tests\Unit;

use Tests\NonPublicMethodTool;
use Tests\UnitCase;

use Framekit\AggregateRoot;
use Framekit\Exceptions\MethodUnknown;

/**
 * AggregateRoot unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class AggregateRootTest extends UnitCase
{
    use NonPublicMethodTool;

    public function testGetIdMethod()
    {
        $aggreagate = $this->getMockForAbstractClass(AggregateRoot::class, ['uuid']);

        $this->assertEquals(
            'uuid',
            $aggreagate->getId()
        );
    }

    public function testGetState()
    {
        $aggreagate = $this->getMockForAbstractClass(AggregateRoot::class, ['uuid']);

        $this->assertEquals(
            null,
            $aggreagate->getState()
        );
    }

    public function testThrowWhenMagicCall()
    {
        $this->expectException(MethodUnknown::class);

        $aggreagate = $this->getMockForAbstractClass(AggregateRoot::class, ['uuid']);
        $aggreagate->testNotExisting();
    }

    public function testApplyChangeMethod()
    {
        $event = new \Tests\Components\IntegerAdded(2);
        $aggreagate = $this->getMockBuilder(AggregateRoot::class)
                           ->disableOriginalConstructor()
                           ->setMethods(['applyIntegerAdded'])
                           ->getMockForAbstractClass();

        $aggreagate->expects($this->once())
                   ->method('applyIntegerAdded')
                   ->with($this->equalTo($event));

        $compose = self::getMethodOfClass(AggregateRoot::class, 'applyChange');
        $compose->invokeArgs($aggreagate, [$event]);
    }
}
