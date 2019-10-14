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
class AggregateRemoved extends Event
{
    /**
     * @var string
     */
    public $aggregateId;

    /**
     * @var \Carbon\Carbon
     */
    public $deletedAt;

    /**
     * @param string         $aggregateId
     * @param \Carbon\Carbon $created_at
     */
    public function __construct(string $aggregateId, Carbon $deleted_at = null)
    {
        parent::__construct();
        
        $this->aggregateId = $aggregateId;
        $this->deletedAt   = $deleted_at ?? Carbon::now();
    }
}
