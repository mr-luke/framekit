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
     * Usage:
     * $compose = self::getMethodOfClass(Class::class, 'method');
     * $compose->setAccessible(true;)
     * $compose->invokeArgs($classInstance, [$args]);
     *
     * @param string $class
     * @param string $method
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    protected static function getMethodOfClass(string $class, string $method): ReflectionMethod
    {
        return (new ReflectionClass($class))->getMethod($method);
    }
}
