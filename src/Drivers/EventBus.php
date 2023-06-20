<?php

declare(strict_types=1);

namespace Framekit\Drivers;

use Framekit\Contracts\EventBus as EventBusContract;
use Framekit\Event;
use Framekit\Exceptions\MissingReactor;
use Illuminate\Contracts\Container\Container;
use Illuminate\Log\Logger;
use Mrluke\Bus\Contracts\Handler;
use Mrluke\Bus\Contracts\HasAsyncProcesses;
use Mrluke\Bus\Contracts\Process;
use Mrluke\Bus\Contracts\ProcessRepository;
use Mrluke\Bus\Contracts\ShouldBeAsync;
use Mrluke\Bus\Contracts\Trigger;
use Mrluke\Bus\Exceptions\InvalidHandler;
use Mrluke\Bus\Extensions\UsesDefaultQueue;
use Mrluke\Bus\MultipleHandlerBus;
use Mrluke\Configuration\Contracts\ArrayHost;
use ReflectionClass;

/**
 * EventBus is responsible for detecting reaction.
 *
 * @author  Łukasz Sitnicki (mr-luke)
 * @package mr-luke/framekit
 * @link    http://github.com/mr-luke/framekit
 * @licence MIT
 *
 * @property mixed queueConnection
 */
class EventBus extends MultipleHandlerBus implements EventBusContract, HasAsyncProcesses
{
    use UsesDefaultQueue;

    protected array $globals = [];

    public bool $persistSyncInstructions = false;

    public bool $throwWhenNoHandler = false;

    /**
     * @param \Mrluke\Configuration\Contracts\ArrayHost $config
     * @param \Mrluke\Bus\Contracts\ProcessRepository   $repository
     * @param \Illuminate\Contracts\Container\Container $container
     * @param \Illuminate\Log\Logger                    $logger
     * @param null                                      $queueResolver
     * @throws \Mrluke\Bus\Exceptions\MissingConfiguration
     */
    public function __construct(
        ArrayHost         $config,
        ProcessRepository $repository,
        Container         $container,
        Logger            $logger,
                          $queueResolver = null
    ) {
        parent::__construct($repository, $container, $logger, $queueResolver);

        $this->queueConnection = $config->get('queues.event_bus');
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function eventReactors(): array
    {
        return $this->handlers;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function globalReactors(): array
    {
        return $this->globals;
    }

    /**
     * @inheritDoc
     */
    public function mapGlobals(array $stack): void
    {
        foreach ($stack as $c) {
            $reflection = new ReflectionClass($c);

            if (
                !$reflection->isInstantiable() ||
                !$reflection->implementsInterface(Handler::class)
            ) {
                throw new InvalidHandler(
                    sprintf('Handler must be an instance of %s', Handler::class)
                );
            }
        }

        $this->globals = array_merge($this->globals, $stack);
    }

    /**
     * @inheritDoc
     */
    public function publish(Event $event): ?Process
    {
        if (!$this->hasHandler($event)) {
            if (!$this->throwWhenNoHandler) {
                return null;
            }

            $this->throwOnMissingHandler($event);
        }

        $handlers = array_merge(
            $this->globalReactors(),
            $this->handler($event)
        );
        $process = $this->createProcess($event, $handlers);

        $this->processHandlersStack(
            $event,
            $process,
            $handlers,
            $event instanceof ShouldBeAsync
        );

        return $process;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    protected function getBusName(): string
    {
        return 'event-bus';
    }

    /**
     * Throw exception when handler is missing.
     *
     * @param \Mrluke\Bus\Contracts\Trigger $trigger
     * @throws \Framekit\Exceptions\MissingReactor
     */
    protected function throwOnMissingHandler(Trigger $trigger): void
    {
        throw new MissingReactor(
            sprintf('Missing handler for the event [%s]', get_class($trigger))
        );
    }
}
