<?php

declare(strict_types=1);

namespace Framekit\Contracts;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
interface DataTransferObject
{
    /**
     * Cast all attributes to array.
     *
     * @return array
     */
    public function toArray(): array;
}
