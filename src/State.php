<?php

declare(strict_types=1);

namespace Framekit;

use Carbon\Carbon;
use Framekit\Contracts\Serializable;

/**
 * State contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
abstract class State implements Serializable
{
    /**
     * Aggregate state identifier.
     *
     * @var string
     */
    public $id;

    /**
     * Date of aggragate creation.
     *
     * @var \Carbon\Carbon;
     */
    protected $createdAt;

    /**
     * Date of aggragate creation.
     *
     * @var \Carbon\Carbon;
     */
    protected $deletedAt;

    /**
     * @param mixed          $id
     * @param \Carbon\Carbon $createdAt
     */
    public function __construct($id, Carbon $createdAt)
    {
        $this->id        = $id;
        $this->createdAt = $createdAt;
    }

    /**
     * @return \Carbon\Carbon|null
     */
    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    /**
     * @return \Carbon\Carbon|null
     */
    public function getDeletedAt(): ?Carbon
    {
        return $this->deletedAt;
    }

    /**
     * Create new instance with date.
     *
     * @param  mixed $id
     * @return self
     */
    public static function init($id): self
    {
        return new static(
            $id,
            Carbon::now()
        );
    }

    /**
     * Mark state as deleted.
     *
     * @param  \Carbon\Carbon  $deletedAt
     * @return void
     */
    public function markAsRemoved(Carbon $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
