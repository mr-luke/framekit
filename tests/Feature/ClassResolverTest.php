<?php

namespace Tests\Feature;

use Tests\AppCase;
use Tests\Components\ResolveTest;

use Framekit\Extentions\ClassResolver;
use Illuminate\Http\Request;

/**
 * ClassResolver app tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class ClassResolverTest extends AppCase
{
    public function testResolveClassMethod()
    {
        $mock = $this->getMockBuilder(ClassResolver::class)
                     ->setConstructorArgs([$this->app])
                     ->getMockForTrait();

        $class = $mock->resolveClass(ResolveTest::class);

        $this->assertInstanceOf(
            ResolveTest::class,
            $class
        );

        $this->assertInstanceOf(
            Request::class,
            $class->class
        );
    }
}
