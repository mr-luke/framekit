<?php

declare(strict_types=1);

namespace Framekit\Exceptions;

use Exception;
use Throwable;

/**
 * @author    Hubert Smusz <hubert.smusz@movecloser.pl>
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class MultipleInvariantViolation extends Exception
{
    private array $errors;

    public function __construct(
        array     $errors,
        ?string   $message = '',
        int       $code = 409,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
