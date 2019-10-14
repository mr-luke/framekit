<?php

declare(strict_types=1);

namespace Framekit\Eventing;

use Framekit\Contracts\Bus;
use Framekit\Contracts\Projector;
use Framekit\Contracts\Retrospector as Contract;
use Framekit\Contracts\Store;
use Framekit\Retrospection;

/**
 * Retrospector class for Framekit.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 * @version   1.0.0
 */
class Retrospector implements Contract
{
    /**
     * @var \Framekit\Contracts\Bus
     */
    protected $eventBus;

    /**
     * @var \Framekit\Contracts\Store
     */
    protected $eventStore;

    /**
     * @var \Framekit\Contracts\Projector
     */
    protected $projector;

    /**
     * @param \Framekit\Contracts\Bus       $bus
     * @param \Framekit\Contracts\Store     $store
     * @param \Framekit\Contracts\Projector $projector
     */
    function __construct(Bus $bus, Store $store, Projector $projector)
    {
        $this->eventBus    = $bus;
        $this->eventStore  = $store;
        $this->projector   = $projector;
    }

    /**
     * Perform given retrospection.
     *
     * @param  \Framekit\Retrospection  $retrospection
     * @return void
     */
    public function perform(Retrospection $retrospection): void
    {
        $streams = $this->eventStore->getAvailableStreams();

        if (
            isset($retrospection->filterStreams['include']) ||
            isset($retrospection->filterStreams['exclude'])
        ) {
            $streams = $this->filterStreams($streams, $map);
        }

        foreach ($streams as $s) {
            $events = $this->eventStore->loadStream($s['stream_id']);

            foreach ($events as $e) {

                $e = $retrospection->preAction($e);

                if ($retrospection->useProjections) {
                    $this->projector->projectByEvent($s['stream_type'], $event);
                }

                if ($retrospection->useReactors) {
                    // TODO: filter reactors
                    $this->eventBus->publish($e);
                }

                $retrospection->postAction($e);
            }
        }
    }

    /**
     * Filter streams.
     *
     * @param  array $stream
     * @param  array $map
     * @return array
     */
    protected function filterStreams(array $stream, array $map): array
    {
        if (isset($map['include']) && isset($map['exclude'])) {
            throw new \InvalidArgumentException(
                'Invalid Retrospection configuration. [include] & [exclude] not allowed simultanously'
            );
        }

        if (isset($map['include']) && count($map['include'])) {
            // TODO: Filter streams
        } elseif (isset($map['exclude']) && count($map['exclude'])) {
            // TODO: Filter streams
        }

        return $stream;
    }
}
