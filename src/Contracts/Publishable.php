<?php

declare(strict_types=1);

namespace Framekit\Contracts;

use Mrluke\Bus\Contracts\Instruction;
use Mrluke\Bus\Contracts\Trigger;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
interface Publishable extends Instruction, Trigger { }
