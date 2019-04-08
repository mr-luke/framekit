<?php

declare(strict_types=1);

namespace Framekit;

use InvalidArgumentException;

/**
 * Value Object contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 *
 * @codeCoverageIgnore
 */
abstract class ValueObject
{
    /**
     * Return attribute of ValueObject.
     *
     * @param  string  $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (! property_exists ($this, $name)) {
            throw new InvalidArgumentException(
                sprintf('Trying to access non-existing property %s', $name)
            );
        }

        return $this->{$name};
    }
}
