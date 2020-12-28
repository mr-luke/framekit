<?php

declare(strict_types=1);

namespace Framekit\Drivers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Log\Logger;
use Mrluke\Bus\Contracts\AsyncBus;
use Mrluke\Bus\Contracts\Process;
use Mrluke\Bus\Contracts\ProcessRepository;
use Mrluke\Bus\Extensions\UsesDefaultQueue;
use Mrluke\Bus\MultipleHandlerBus;
use Mrluke\Configuration\Contracts\ArrayHost;

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
class Projector extends MultipleHandlerBus implements ProjectorContract, AsyncBus
{
    use UsesDefaultQueue;

    /** Determine if process should be delete on success.
     *
     * @var bool
     */
    public bool $cleanOnSuccess = false;

    /**
     * Determine if Bus should stop executing on exception.
     *
     * @var bool
     */
    public bool $stopOnException = false;

    /**
     * Determine if Bus should throw if there's no handler to process.
     *
     * @var bool
     */
    public bool $throwWhenNoHandler = false;

    /**
     * @param \Mrluke\Configuration\Contracts\ArrayHost $config
     * @param \Mrluke\Bus\Contracts\ProcessRepository   $repository
     * @param \Illuminate\Contracts\Container\Container $container
     * @param \Illuminate\Log\Logger                    $logger
     * @param null                                      $queueResolver
     */
    public function __construct(
        ArrayHost $config,
        ProcessRepository $repository,
        Container $container,
        Logger $logger,
        $queueResolver = null
    ) {
        parent::__construct($repository, $container, $logger, $queueResolver);

        $this->queueName = $config->get('queues.projector');
    }

    /**
     * Return registered Projections list.
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function aggregateProjections(): array
    {
        return $this->handlers;
    }

    /**
     * @inheritDoc
     */
    public function project(AggregateRoot $aggregate): array
    {
        return $this->dispatchMultiple(
            $aggregate,
            $aggregate->unpublishedEvents()
        );
    }

    /**
     * @inheritDoc
     */
    public function projectByEvents(AggregateRoot $aggregate, array $events): array
    {
        return $this->dispatchMultiple(
            $aggregate,
            $events
        );
    }

    /**
     * @inheritDoc
     */
    public function projectSingle(AggregateRoot $aggregate, Event $event): Process
    {
        return $this->dispatch($event, $aggregate);
    }

    /**
     * @inheritDoc
     */
    protected function getBusName(): string
    {
        return 'projector';
    }
}
