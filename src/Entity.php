<?php

declare(strict_types=1);

namespace Framekit;

use Carbon\Carbon;
use Framekit\Contracts\Serializable;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
abstract class Entity implements Serializable
{
    /**
     * Aggregate state identifier.
     *
     * @var string
     */
    public mixed $id;

    /**
     * Date of aggregate creation.
     *
     * @var \Carbon\Carbon;
     */
    protected Carbon $createdAt;

    /**
     * Date of aggregate creation.
     *
     * @var \Carbon\Carbon|null;
     */
    protected ?Carbon $deletedAt = null;

    /**
     * @param mixed          $id
     * @param \Carbon\Carbon $createdAt
     */
    public function __construct(mixed $id, Carbon $createdAt)
    {
        $this->id        = $id;
        $this->createdAt = $createdAt;
    }

    /**
     * Return time when Entity was created.
     *
     * @return \Carbon\Carbon|null
     */
    public function createdAt(): ?Carbon
    {
        return $this->createdAt;
    }

    /**
     * Create new instance with current date.
     *
     * @param  mixed $id
     * @return self
     */
    public static function createWithCurrentTime(mixed $id): self
    {
        return new static(
            $id,
            Carbon::now()
        );
    }

    /**
     * Return time when Entity was deleted.
     *
     * @return \Carbon\Carbon|null
     */
    public function deletedAt(): ?Carbon
    {
        return $this->deletedAt;
    }

    /**
     * Determine if Entity has already been deleted.
     *
     * @return bool
     */
    public function isAlreadyDeleted(): bool
    {
        return !empty($this->deletedAt);
    }

    /**
     * Mark Entity as deleted now.
     *
     * @return void
     */
    public function markAsRemoved(): void
    {
        $this->markAsRemovedAtDate(
            Carbon::now()
        );
    }

    /**
     * Mark Entity as deleted at specific date.
     *
     * @param  \Carbon\Carbon  $deletedAt
     * @return void
     */
    public function markAsRemovedAtDate(Carbon $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
