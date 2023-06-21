<?php

namespace Framekit\Exceptions;

use Exception;
use Throwable;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class InvariantViolation extends Exception
{
    /**
     * @param string          $message
     * @param int             $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 409, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
