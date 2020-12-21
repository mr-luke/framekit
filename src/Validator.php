<?php

declare(strict_types=1);

namespace Framekit;

/**
 * Validator contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 *
 * @codeCoverageIgnore
 */
abstract class Validator
{
    /**
     * Validate domain invariant.
     *
     * @return void
     * @throws \Framekit\Exceptions\InvariantViolation
     */
    abstract public function protectInvariant(): void;

    /**
     * Return attribute of ValueObject.
     *
     * @param mixed ...$params
     * @return void
     * @throws \Framekit\Exceptions\InvariantViolation
     */
    public static function validate(...$params): void
    {
        $validator = new static(...$params);
        $validator->protectInvariant();
    }
}
