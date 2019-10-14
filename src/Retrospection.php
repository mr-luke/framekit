<?php

declare(strict_types=1);

namespace Framekit;

/**
 * Retrospection abstract class.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
abstract class Retrospection
{
    /**
     * Determine which reactors should be ommitter.
     *
     * @var array
     */
    protected $filterReactors = [];

    /**
     * Determine which stream should be ommitter.
     *
     * @var array
     */
    protected $filterStreams = [];

    /**
     * Deremine if retrospection should project events.
     *
     * @var boolean
     */
    protected $useProjections = true;

    /**
     * Deremine if retrospection should publish events.
     *
     * @var boolean
     */
    protected $useReactors = true;

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
