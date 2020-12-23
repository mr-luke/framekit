<?php

declare(strict_types=1);

namespace Framekit\Drivers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Log\Logger;
use Illuminate\Routing\Pipeline;
use Mrluke\Bus\Contracts\Handler;
use Mrluke\Bus\Contracts\HasAsyncProcesses;
use Mrluke\Bus\Contracts\Instruction;
use Mrluke\Bus\Contracts\Process;
use Mrluke\Bus\Contracts\ProcessRepository;
use Mrluke\Bus\Contracts\ShouldBeAsync;
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
 * @version 2.0.0
 */
class EventBus extends MultipleHandlerBus implements EventBusContract, HasAsyncProcesses
{
    use UsesDefaultQueue;

    /** Determine if process should be delete on success.
     *
     * @var bool
     */
    protected $cleanOnSuccess = false;

    /**
     * Register of global Reactors.
     *
     * @var array
     */
    protected $globals = [];

    /**
     * Determine if Bus should stop executing on exception.
     *
     * @var bool
     */
    protected $stopOnException = false;

    public function __construct(
        ArrayHost $config,
        ProcessRepository $repository,
        Container $container,
        Pipeline $pipeline,
        Logger $logger,
        $queueResolver = null
    ) {
        parent::__construct($repository, $container, $pipeline, $logger, $queueResolver);

        $this->queueConnection = $config->get('queues.event_bus');
    }

    /**
     * Return registered global Reactors list.
     *
     * @return array
     * @throws \Mrluke\Bus\Exceptions\InvalidHandler
     * @throws \ReflectionException
     */
    public function globalHandler(): array
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
     * @param bool            $cleanOnSuccess
     * @return \Mrluke\Bus\Contracts\Process
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Mrluke\Bus\Exceptions\InvalidAction
     * @throws \Mrluke\Bus\Exceptions\InvalidHandler
     * @throws \Mrluke\Bus\Exceptions\MissingConfiguration
     * @throws \Mrluke\Bus\Exceptions\MissingHandler
     * @throws \ReflectionException
     */
    public function publish(Event $event, bool $cleanOnSuccess = false): Process
    {
        if (!$this->hasHandler($event)) {
            $this->throwOnMissingHandler($event);
        }

        $handler = array_merge(
            $this->globalHandler(),
            $this->handler($event)
        );

        if ($event instanceof ShouldBeAsync) {
            /** @var Instruction $event */
            return $this->runAsync(
                $event,
                $handler,
                $this->considerCleaning($cleanOnSuccess)
            );
        }

        return $this->run($event, $handler, $this->considerCleaning($cleanOnSuccess));
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
     * @param \Mrluke\Bus\Contracts\Instruction $instruction
     * @throws \Mrluke\Bus\Exceptions\MissingHandler
     */
    protected function throwOnMissingHandler(Instruction $instruction): void
    {
        throw new MissingReactor(
            sprintf('Missing handler for the event [%s]', get_class($instruction))
        );
    }
}
