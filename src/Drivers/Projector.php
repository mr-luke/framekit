<?php

declare(strict_types=1);

namespace Framekit\Drivers;

use InvalidArgumentException;
use Mrluke\Bus\AbstractBus;
use Mrluke\Bus\Contracts\Process;

use Framekit\AggregateRoot;
use Framekit\Contracts\Projector as ProjectorContract;
use Framekit\Event;

/**
 * Projector is responsible for projecting changes to DB.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class Projector extends AbstractBus implements ProjectorContract
{
    /**
     * Project changes for given aggregate.
     *
     * @param \Framekit\AggregateRoot $aggregate
     * @param array                   $events
     * @return \Mrluke\Bus\Contracts\Process
     */
    public function project(AggregateRoot $aggregate, array $events): Process
    {
        // @TODO: refactor
        $projection = $this->getProjection(get_class($aggregate));

        foreach ($events as $e) {
            if (!$e instanceof Event) {
                throw new InvalidArgumentException(
                    sprintf('Projected events must be instance of %s', Event::class)
                );
            }

            $projection->handle($e);
        }
    }

    /**
     * Project changes for given aggregate.
     *
     * @param string          $aggregate
     * @param \Framekit\Event $event
     * @return void
     *
     * @throws \Framekit\Exceptions\MissingProjection
     * @throws \ReflectionException
     */
    public function projectByEvent(string $aggregate, Event $event): Process
    {
        $projection = $this->getProjection($aggregate);
        $projection->handle($event);
    }

    /**
     * @inheritDoc
     */
    protected function getBusName(): string
    {
        return 'projector';
    }
}
