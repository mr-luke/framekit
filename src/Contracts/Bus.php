<?php

declare(strict_types=1);

namespace Framekit\Contracts;

/**
 * Bus contract.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
interface Bus
{
    /**
     * Return registered Reducers list.
     *
     * @return array
     */
    public function handlers(): array;

    /**
     * Handle Publishable with coresponding Handler.
     *
     * @param  \Framekit\Contracts\Publishable  $source
     * @return void
     */
    public function publish(Publishable $source): void;

    /**
     * Register Reactors stack.
     *
     * @param  array $stack
     * @return void
     */
    public function register(array $stack): void;
}
