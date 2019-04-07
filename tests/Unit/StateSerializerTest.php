<?php

namespace Tests\Unit;

use Tests\UnitCase;

use Framekit\Contracts\Serializer;
use Framekit\Eventing\StateSerializer;
use Framekit\State;

/**
 * StateSerializer unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class StateSerializerTest extends UnitCase
{
    public function testClassResolveContract()
    {
        $this->assertInstanceOf(
            Serializer::class,
            new StateSerializer
        );
    }

    public function testSerializeClass()
    {
        $mock = $this->getMockBuilder(State::class)
                     ->disableOriginalConstructor()
                     ->getMock();

        $serializer = new StateSerializer;
        $after = $serializer->serialize($mock);

        $this->assertEquals(
            serialize($mock),
            $after
        );
    }

    public function testUnserializeClass()
    {
        $mock = $this->getMockBuilder(State::class)
                     ->disableOriginalConstructor()
                     ->getMock();
        $serilized = serialize($mock);

        $serializer = new StateSerializer;
        $class = $serializer->unserialize($serilized);

        $this->assertEquals(
            $mock,
            $class
        );
    }
}
