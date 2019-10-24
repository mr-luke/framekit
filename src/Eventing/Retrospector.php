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
        $this->eventBus   = $bus;
        $this->eventStore = $store;
        $this->projector  = $projector;
    }

    /**
     * Perform given retrospection.
     *
     * @param \Framekit\Retrospection $retrospection
     *
     * @return void
     */
    public function perform(Retrospection $retrospection): void
    {
        if (
            isset($retrospection->filterReactors['include']) ||
            isset($retrospection->filterReactors['exclude'])
        ) {
            $handlers = $this->filterReactors($this->eventBus->handlers(), $retrospection->filterReactors);
            $this->eventBus->replace($handlers);
        }

        $streams = $this->eventStore->getAvailableStreams();

        if (
            isset($retrospection->filterStreams['include']) ||
            isset($retrospection->filterStreams['exclude'])
        ) {
            $streams = $this->filterStreams($streams, $retrospection->filterStreams);
        }

        foreach ($streams as $s) {
            $events = $this->eventStore->loadStream($s['stream_id'], true);

            foreach ($events as $e) {

                $e = $retrospection->preAction($e);

                if ($retrospection->useProjections) {
                    $this->projector->projectByEvent($s['stream_type'], $e);
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
     * @param array $stream
     * @param array $map
     *
     * @return array
     */
    public static function filterStreams(array $stream, array $map): array
    {
        self::validateMap($map);

        if (isset($map['include']) && count($map['include'])) {
            $stream = array_filter($stream, function ($item) use ($map) {
                return in_array($item['stream_id'], $map['include']);
            });
        } elseif (isset($map['exclude']) && count($map['exclude'])) {
            $stream = array_filter($stream, function ($item) use ($map) {
                return !in_array($item['stream_id'], $map['exclude']);
            });
        }

        return $stream;
    }

    /**
     * @param array $handlers
     * @param array $map
     *
     * @return array
     */
    public static function filterReactors(array $handlers, array $map): array
    {
        self::validateMap($map);

        $filteredHandlers = [];
        if (isset($map['include']) && count($map['include'])) {
            foreach ($handlers as $event => $handler) {
                if (is_array($handler)) {
                    $allowedHandlers = array_values(array_intersect($handler, $map['include']));
                    if (!empty($allowedHandlers)) {
                        $filteredHandlers[$event] = $allowedHandlers;
                    }
                } else {
                    if (in_array($handler, $map['include'])) {
                        $filteredHandlers[$event] = $handler;
                    }
                }
            }
        } elseif (isset($map['exclude']) && count($map['exclude'])) {
            foreach ($handlers as $event => $handler) {
                if (is_array($handler)) {
                    $allowedHandlers = array_values(array_diff($handler, $map['exclude']));
                    if (!empty($allowedHandlers)) {
                        $filteredHandlers[$event] = $allowedHandlers;
                    }
                } else {
                    if (!in_array($handler, $map['exclude'])) {
                        $filteredHandlers[$event] = $handler;
                    }
                }
            }
        }

        return $filteredHandlers;
    }

    /**
     * @param array $map
     */
    private static function validateMap(array $map): void
    {
        if (isset($map['include']) && isset($map['exclude'])) {
            throw new \InvalidArgumentException(
                'Invalid Retrospection configuration. [include] & [exclude] not allowed simultanously'
            );
        }
    }
}
