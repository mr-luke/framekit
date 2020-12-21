<?php

declare(strict_types=1);

namespace Framekit\Drivers;

use Illuminate\Contracts\Container\Container;
use Mrluke\Bus\Extensions\ResolveDependencies;

use Framekit\Contracts\Mapper;

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
    protected $container;

    /**
     * Register of Event->Mapper pairs.
     *
     * @var array
     */
    protected $register;

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
     * @param array $payload
     * @param int   $from
     * @param array $upstream
     * @return array
     */
    public function map(array $payload, int $from, array $upstream): array
    {
        return $payload;
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
        $this->register = array_merge($this->register, $stack);
    }
}
