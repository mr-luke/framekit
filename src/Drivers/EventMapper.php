<?php

declare(strict_types=1);

namespace Framekit\Drivers;

use Illuminate\Foundation\Application;

use Framekit\Contracts\Mapper;
use Framekit\Extentions\ClassResolver;

/**
 * Event Mapper maps event between versions.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
final class EventMapper implements Mapper
{
    use ClassResolver;

    /**
     * Register of Event->Mapper pairs.
     *
     * @var array
     */
    protected $register;

    /**
     * @param array $stack
     */
    public function __construct(Application $app, array $stack = [])
    {
        $this->app      = $app;
        $this->register = $stack;
    }

    /**
     * Map event to newest version.
     *
     * @param  array  $payload
     * @param  int    $from
     * @param  array  $upstream
     * @return void
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
     * @param  array $stack
     * @return void
     */
    public function register(array $stack): void
    {
        $this->register = array_merge($this->register, $stack);
    }
}
