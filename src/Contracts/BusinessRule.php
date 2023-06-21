<?php

declare(strict_types=1);

namespace Framekit\Contracts;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
interface BusinessRule
{
    /**
     * Determine if an invariant is broken.
     *
     * @return bool
     */
    public function isBroken(): bool;

    /**
     * Returns message associated with the invariant.
     *
     * @return string
     */
    public function message(): string;
}
