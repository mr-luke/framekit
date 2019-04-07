<?php

declare(strict_types=1);

namespace Framekit;

use Framekit\Contracts\Publishable;
use Framekit\Contracts\Serializable;

/**
 * Event contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
abstract class Event implements Publishable, Serializable
{
    /**
     * Determine version of an event.
     *
     * @var int
     */
    public static $eventVersion = 1;
}
