<?php

declare(strict_types=1);

namespace Framekit\Drivers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Log\Logger;
use Mrluke\Bus\Contracts\Handler;
use Mrluke\Bus\Contracts\HasAsyncProcesses;
use Mrluke\Bus\Contracts\Instruction;
use Mrluke\Bus\Contracts\Process;
use Mrluke\Bus\Contracts\ProcessRepository;
use Mrluke\Bus\Contracts\ShouldBeAsync;
use Mrluke\Bus\Contracts\Trigger;
use Mrluke\Bus\Exceptions\InvalidHandler;
use Mrluke\Bus\Extensions\UsesDefaultQueue;
use Mrluke\Bus\MultipleHandlerBus;
use Mrluke\Configuration\Contracts\ArrayHost;
use ReflectionClass;

use Framekit\Contracts\EventBus as EventBusContract;
use Framekit\Event;
use Framekit\Exceptions\MissingReactor;

/**
 * EventBus is responsible for detecting reaction.
 *
 * @author  Łukasz Sitnicki (mr-luke)
 * @package mr-luke/framekit
 * @link    http://github.com/mr-luke/framekit
 * @licence MIT
 * @version 2.0.0`
 *
 * @property mixed queueConnection
 */
class EventBus extends MultipleHandlerBus implements EventBusContract, HasAsyncProcesses
{
    use UsesDefaultQueue;

    /** Determine if process should be delete on success.
     *
     * @var bool
     */
    public bool $cleanOnSuccess = false;

    /**
     * Register of global Reactors.
     *
     * @var array
     */
    protected array $globals = [];

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
     * @throws \Mrluke\Bus\Exceptions\MissingConfiguration
     */
    public function __construct(
        ArrayHost $config,
        ProcessRepository $repository,
        Container $container,
        Logger $logger,
        $queueResolver = null
    ) {
        parent::__construct($repository, $container, $logger, $queueResolver);

        $this->queueConnection = $config->get('queues.event_bus');
    }

    /**
     * Return all registered event reactors.
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function eventReactors(): array
    {
        return $this->handlers;
    }

    /**
     * Return registered global Reactors list.
     *
     * @return array
     * @throws \Mrluke\Bus\Exceptions\InvalidHandler
     * @throws \ReflectionException
     */
    public function globalReactors(): array
    {
        foreach ($this->globals as $h) {
            $reflection = new ReflectionClass($h);

            if (
                !$reflection->isInstantiable() ||
                !$reflection->implementsInterface(Handler::class)
            ) {
                throw new InvalidHandler(
                    sprintf('Handler must be an instance of %s', Handler::class)
                );
            }
        }

        return $this->globals;
    }

    /**
     * Register Reactors stack.
     *
     * @param array $stack
     * @return void
     */
    public function mapGlobals(array $stack): void
    {
        $this->globals = array_merge($this->globals, $stack);
    }

    /**
     * Publish Event to it's reactors.
     *
     * @param \Framekit\Event $event
     * @return \Mrluke\Bus\Contracts\Process|null
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Mrluke\Bus\Exceptions\InvalidAction
     * @throws \Mrluke\Bus\Exceptions\InvalidHandler
     * @throws \Mrluke\Bus\Exceptions\MissingConfiguration
     * @throws \Mrluke\Bus\Exceptions\MissingHandler
     * @throws \Mrluke\Bus\Exceptions\MissingProcess
     * @throws \ReflectionException
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
