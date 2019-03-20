<?php

namespace Mrluke\Framekit;

use Mrluke\Framekit\Event;
use Mrluke\Framekit\Contracts\Stream;
use Mrluke\Framekit\Facades\Handler;
use Mrluke\Framekit\Facades\Store;
use Mrluke\Framekit\State;

/**
 * Aggregate abstract class.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
abstract class Aggregate
{
    /**
     * Aggregate Id.
     *
     * @var string
     */
    public $id;

    /**
     * Events stream already applied.
     *
     * @var array
     */
    protected $events = [];

    /**
     * Last commited event of stream.
     *
     * @var int
     */
    protected $last;

    /**
     * State of aggregate.
     *
     * @var \Mrluke\Framekit\State
     */
    protected $state;

    /**
     * Determine which class contains state of an Aggregate.
     *
     * @var string
     */
    const STATECLASS = self::STATECLASS;

    /**
     * @param  string                  $aggreagateId
     * @param  \Mrluke\Framekit\State  $state
     * @param  int                     $last
     * @return void
     */
    public function __construct(string $aggreagateId, State $state, int $last = 0): void
    {
        $this->id    = $aggreagateId;
        $this->state = $state;
        $this->last  = $last;
    }

    /**
     * Rebuild aggrate state based on stream only/and state.
     *
     * @param  \Mrluke\Framekit\Contracts\Stream  $stream
     * @param  \Mrluke\Framekit\State|null        $state
     * @param  int                                $last
     * @return \Mrluke\Framekit\Aggregate
     */
    public static function rebuildState(Stream $stream, State $state = null, int $last = 0): Aggregate
    {
        if (is_null($state)) {
            $state = new self::STATECLASS;
        }

        $aggreagate = new self($stream->getId(), $state, $last);
        $aggreagate->replyEvents($stream);

        return $aggreagate;
    }

    /**
     * Apply new Event on aggregate state.
     *
     * @param  \Mrluke\Framekit\Event $event
     * @return void
     */
    protected function applyEvent(Event $event): void
    {
        $this->last  = Store::storeEvent($event, $this->id);
        $this->state = Handler::fireOnState($event, $state);

        $this->events[] = [
            'id'      => $this->last,
            'event'   => get_class($event),
            'payload' => Store::serialize($event, 'event')
        ];
    }

    /**
     * Reply EventStream on aggregate state.
     *
     * @param  \Mrluke\Framekit\Contracts\Stream $stream
     * @return void
     */
    private function replyEvents(Stream $stream): void
    {
        foreach ($stream->getIterator() as $event) {
            // Each event need to be  added to events list of agragate.
            // In case of duplicated mutation event_id is compare with
            // last processed for an actual state.
            //

            $this->events[] = [
                'id'      => $event->id,
                'event'   => $event->event,
                'payload' => $event->payload
            ];

            if ($this->last >= $event->id) {
                continue;
            }

            $this->state = Handler::applyOnState(
                Store::unserialize($event->payload, 'event'),
                $state
            );
            $this->last = $event->sequence_no;
        }
    }
}
