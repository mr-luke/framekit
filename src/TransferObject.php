<?php

declare(strict_types=1);

namespace Framekit;

use Framekit\Contracts\DTO;
use InvalidArgumentException;

/**
 * Data Transfer Object.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class TransferObject implements DTO
{
    /**
     * Attributes of transfered object.
     *
     * @var array
     */
    protected $attributes;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Return attribute of ValueObject.
     *
     * @param  string  $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (! array_key_exists($name, $this->attributes)) {
            throw new InvalidArgumentException(
                sprintf('Trying to access non-existing property %s', $name)
            );
        }

        return $this->attributes[$name];
    }

    /**
     * Cast all attributes to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
