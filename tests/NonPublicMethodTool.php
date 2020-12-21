<?php

namespace Tests;

use ReflectionClass;
use ReflectionMethod;

/**
 * Tool for testing non-public methods.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
trait NonPublicMethodTool
{
    /**
     * Tool for testing protected and private methods.
     *
     * Usage:
     * $compose = self::getMethodOfClass(Class::class, 'method');
     * $compose->invokeArgs($classInstance, [$args]);
     *
     * @param  string $class
     * @param  string $method
     * @return \ReflectionMethod
     */
    protected static function getMethodOfClass(string $class, string $method): ReflectionMethod
    {
        $method = (new ReflectionClass($class))->getMethod($method);
        $method->setAccessible(true);
        return $method;
    }
}
