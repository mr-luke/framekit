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
 * @license   MIT
 */
class AggregateCreated extends Event
{
    /**
     * @var string
     */
    public $aggregateId;

    /**
     * @var \Carbon\Carbon
     */
    public $createdAt;

    /**
     * @param string         $aggregateId
     * @param \Carbon\Carbon $created_at
     */
    public function __construct(string $aggregateId, Carbon $created_at = null)
    {
        $this->aggreagateId = $aggregateId;
        $this->createdAt    = $created_at ?? Carbon::now();
    }
}
