<?php

declare(strict_types=1);

namespace Framekit;

use Framekit\Contracts\DataTransferObject;
use InvalidArgumentException;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class TransferObject implements DataTransferObject
{
    /**
     * Attributes of transferred object.
     *
     * @var array
     */
    protected array $attributes;

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
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (!array_key_exists($name, $this->attributes)) {
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
     * @codeCoverageIgnore
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
