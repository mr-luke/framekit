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
 * @license   MIT
 */
abstract class Event implements Publishable, Serializable
{
    /**
     * Id of aggregate that fired event.
     *
     * @var string
     */
    public $aggregateId;

    /**
     * Determine version of an event.
     *
     * @var int
     */
    public static $eventVersion = 1;

    /**
     * Microtime when event has been fired.
     *
     * @var int
     */
    public $firedAt;

    public function __construct()
    {
        $now = Carbon::now();

        $this->firedAt = ($now->timestamp * 1000000) + $now->micro;
    }
}
