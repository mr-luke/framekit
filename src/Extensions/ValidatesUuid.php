<?php

namespace Framekit\Extensions;

/**
 * Extensions adds validation of UUID.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
trait ValidatesUuid
{
    /**
     * Check if a given string is a valid UUID
     *
     * @param  $candidate
     * @return  bool
     */
    public function isValidUuid($candidate): bool
    {
        if (
            !is_string($candidate)
            || (preg_match(
                    '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
                    $candidate
                ) !== 1)
        ) {
            return false;
        }

        return true;
    }
}
