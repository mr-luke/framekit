<?php

declare(strict_types=1);

namespace Framekit\Drivers;

use Framekit\Contracts\Mapper;
use Framekit\Contracts\VersionMap;
use Illuminate\Contracts\Container\Container;
use Mrluke\Bus\Extensions\ResolveDependencies;
use ReflectionClass;

/**
 * Event Mapper maps event between versions.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
final class EventMapper implements Mapper
{
    use ResolveDependencies;

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected Container $container;

    /**
     * Register of Event->Mapper pairs.
     *
     * @var array
     */
    protected array $register;

    /**
     * @param \Illuminate\Contracts\Container\Container $container
     * @param array                                     $stack
     */
    public function __construct(Container $container, array $stack = [])
    {
        $this->container = $container;
        $this->register = $stack;
    }

    /**
     * Map event to newest version.
     *
     * @param string $event
     * @param array  $payload
     * @param int    $from
     * @param array  $upstream
     * @return array
     * @throws \Framekit\Exceptions\MethodUnknown
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Mrluke\Bus\Exceptions\RuntimeException
     * @throws \ReflectionException
     */
    public function map(string $event, array $payload, int $from, array $upstream): array
    {
        if (!$this->hasResolvableMap($event)) {
            return $payload;
        }

        return $this->resolveMap($event)->translate(
            $payload,
            $from,
            $event::$__eventVersion__,
            $upstream
        );
    }

    /**
     * Return registered Mappers list.
     *
     * @return array
     */
    public function mappers(): array
    {
        return $this->register;
    }

    /**
     * Register Reactors stack.
     *
     * @param array $stack
     * @return void
     */
    public function register(array $stack): void
    {
        $this->register = array_merge_recursive($this->register, $stack);
    }

    /**
     * Detect if given event has its correct version map class.
     *
     * @param string $event
     * @return bool
     * @throws \ReflectionException
     */
    protected function hasResolvableMap(string $event): bool
    {
        if (!array_key_exists($event, $this->register)) {
            return false;
        }

        $reflection = new ReflectionClass($this->register[$event]);

        return $reflection->isInstantiable() && $reflection->implementsInterface(VersionMap::class);
    }

    /**
     * Create new instance of event's version map.
     *
     * @param string $event
     * @return \Framekit\Contracts\VersionMap
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Mrluke\Bus\Exceptions\RuntimeException
     * @throws \ReflectionException
     */
    protected function resolveMap(string $event): VersionMap
    {
        return $this->resolveClass($this->container, $this->register[$event]);
    }
}
