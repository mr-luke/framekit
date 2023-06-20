<?php

declare(strict_types=1);

namespace Framekit;

use Carbon\Carbon;
use Framekit\Contracts\Publishable;
use Framekit\Contracts\Serializable;

/**
 * Event contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
abstract class Event implements Publishable, Serializable
{
    /**
     * Id of aggregate that fired event.
     *
     * @var string|int
     */
    public string|int $aggregateId;

    /**
     * Determine version of an event.
     *
     * @var int
     */
    public static int $__eventVersion__ = 1;

    /**
     * Micro-time when event has been fired.
     *
     * @var int
     */
    public int $firedAt;

    /**
     * Helper for accessing meta and stream info from event-store
     *
     * @var array
     */
    public array $__meta__ = [];

    /**
     * Event constructor.
     */
    public function __construct()
    {
        $this->firedAt = (int) Carbon::now()->getPreciseTimestamp();
    }
}
