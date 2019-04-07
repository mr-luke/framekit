<?php

declare(strict_types=1);

namespace Tests\Components;

use Framekit\Event;
use Framekit\Reactor;

/**
 * DummyReactor class.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class DummyReactor extends Reactor
{
    /**
     * Handle projection.
     *
     * @param  \Framekit\Event  $event
     * @return void
     */
    public function handle(Event $event): void
    {
        $event->dummy();
    }
}
