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
class AggregateCreated extends Event
{
    /**
     * @var int|string|\Framekit\Contracts\AggregateIdentifier
     */
    public $aggregateId;

    /**
     * @var \Carbon\Carbon
     */
    public $createdAt;

    /**
     * @param int|string|\Framekit\Contracts\AggregateIdentifier $aggregateId
     * @param \Carbon\Carbon                                     $createdAt
     */
    public function __construct($aggregateId, Carbon $createdAt)
    {
        parent::__construct();

        $this->aggregateId = $aggregateId;
        $this->createdAt = $createdAt;
    }
}
