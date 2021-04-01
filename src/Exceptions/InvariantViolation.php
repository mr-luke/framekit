<?php

namespace Framekit\Exceptions;

use Exception;
use Throwable;

class InvariantViolation extends Exception
{
    /**
     * @param string          $message
     * @param int             $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = "", $code = 409, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
