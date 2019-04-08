<?php

declare(strict_types=1);

namespace Framekit;

use InvalidArgumentException;

/**
 * Validator contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 *
 * @codeCoverageIgnore
 */
abstract class Validator
{
    /**
     * Validate domain invariant.
     *
     * @return void
     *
     * @throws InvariantViolation
     */
    abstract public function protectInvariant(): void {}

    /**
     * Return attribute of ValueObject.
     *
     * @param  string  $name
     * @return void
     *
     * @throws InvariantViolation
     */
    public static function validate(...$params): void
    {
        $validator = new static(...$params);
        $validator->protectInvariant();
    }
}
