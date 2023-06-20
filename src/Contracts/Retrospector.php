<?php

declare(strict_types=1);

namespace Framekit\Contracts;

use Framekit\Retrospection;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
interface Retrospector
{
    /**
     * Perform given retrospection.
     *
     * @param  \Framekit\Retrospection  $retrospection
     * @return void
     */
    public function perform(Retrospection $retrospection): void;
}
