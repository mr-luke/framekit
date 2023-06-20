<?php

declare(strict_types=1);

namespace Framekit\Events;

use Carbon\Carbon;
use Framekit\Event;

/**
 * Event fire when aggregate was created.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class AggregateRemoved extends Event
{
    /**
     * @var int|string
     */
    public string|int $aggregateId;

    /**
     * @var \Carbon\Carbon
     */
    public Carbon $deletedAt;

    /**
     * @param int|string     $aggregateId
     * @param \Carbon\Carbon $deletedAt
     */
    public function __construct(int|string $aggregateId, Carbon $deletedAt)
    {
        parent::__construct();

        $this->aggregateId = $aggregateId;
        $this->deletedAt = $deletedAt;
    }
}
