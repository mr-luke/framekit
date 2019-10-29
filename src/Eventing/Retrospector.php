<?php

declare(strict_types=1);

namespace Framekit\Eventing;

use Framekit\Contracts\Bus;
use Framekit\Contracts\Projector;
use Framekit\Contracts\Retrospector as Contract;
use Framekit\Contracts\Store;
use Framekit\Event;
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
     * @param array $handlers
     * @param array $map
     *
     * @return array
     */
    public static function filterReactors(array $handlers, array $map): array
    {
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
     * @param string $streamId
     * @param array  $map
     *
     * @return bool
     */
    public static function filterStreams(string $streamId, array $map): bool
    {
        if (isset($map['include']) && count($map['include'])) {
            return in_array($streamId, $map['include']);
        } elseif (isset($map['exclude']) && count($map['exclude'])) {
            return !in_array($streamId, $map['exclude']);
        } else {
            return true;
        }
    }

    /**
     * @param \Framekit\Event $event
     * @param array           $map
     *
     * @return bool
     */
    public static function filterProjections(Event $event, array $map): bool
    {
        if (isset($map['include']) && count($map['include'])) {
            return in_array(get_class($event), $map['include']);
        } elseif (isset($map['exclude']) && count($map['exclude'])) {
            return !in_array(get_class($event), $map['exclude']);
        } else {
            return true;
        }
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
        $handlers = $this->eventBus->handlers();

        if ($retrospection->useReactors) {
            $this->validateMap($retrospection->filterReactors);
            $handlers = $this->filterReactors($handlers, $retrospection->filterReactors);
            $this->eventBus->replace($handlers);
        }

        $this->validateMap($retrospection->filterStreams);

        if ($retrospection->useProjections) {
            $this->validateMap($retrospection->filterProjections);
        }

        $events = $this->eventStore
            ->loadStream(
                null,
                $retrospection->eventsSince,
                $retrospection->eventsTill,
                true
            );

        foreach ($events as $e) {
            $meta = $e->__meta__;

            if (!$this->filterStreams($meta['stream_id'], $retrospection->filterStreams)) {
                continue;
            }

            $e = $retrospection->preAction($e);

            if ($retrospection->useProjections && $this->filterProjections($e, $retrospection->filterProjections)) {
                $this->projector->projectByEvent($meta['stream_type'], $e);
            }

            if ($retrospection->useReactors) {
                $this->eventBus->publish($e);
            }

            $retrospection->postAction($e);
        }
    }

    /**
     * @param array $map
     */
    private function validateMap(array $map): void
    {
        if (isset($map['include']) && isset($map['exclude'])) {
            throw new \InvalidArgumentException(
                'Invalid Retrospection configuration. [include] & [exclude] not allowed simultanously'
            );
        }
    }
}
