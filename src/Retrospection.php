<?php

declare(strict_types=1);

namespace Framekit;

use Carbon\Carbon;

/**
 * Retrospection abstract class.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 *
 * @codeCoverageIgnore
 */
abstract class Retrospection
{
    /**
     * Determine which reactors should be ommitted.
     *
     * @var array
     */
    public $filterReactors = [];

    /**
     * Determine which stream should be ommitted.
     *
     * @var array
     */
    public $filterStreams = [];

    /**
     * Determine which projections should be ommitted.
     *
     * @var array
     */
    public $filterProjections = [];

    /**
     * Deremine if retrospection should project events.
     *
     * @var boolean
     */
    public $useProjections = true;

    /**
     * Deremine if retrospection should publish events.
     *
     * @var boolean
     */
    public $useReactors = true;

    /**
     * Info for Retrospectors to get Events since this date
     *
     * @var Carbon|null
     */
    public $eventsSince = null;

    /**
     * Info for Retrospectors to get Events till this date
     *
     * @var Carbon|null
     */
    public $eventsTill = null;

    /**
     * Perform post-action on Event.
     *
     * @param  \Framekit\Event  $event
     * @return void
     */
    abstract public function postAction(Event $event): void;

    /**
     * Perform pre-action on Event.
     *
     * @param  \Framekit\Event  $event
     * @return void
     */
    abstract public function preAction(Event $event): Event;
}
