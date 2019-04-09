<?php

declare(strict_types=1);

namespace Framekit\Contracts;

/**
 * DataTransferObject contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
interface DTO
{
    /**
     * Cast all attributes to array.
     *
     * @return array
     */
    public function toArray(): array;
}
