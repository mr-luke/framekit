<?php

namespace Tests\Unit;

use Framekit\TransferObject;
use InvalidArgumentException;
use Tests\UnitCase;

/**
 * TransferObject unit tests.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
class TransferObjectTest extends UnitCase
{
    public function testThrowsWhenAttributeDoesntExist()
    {
        $this->expectException(InvalidArgumentException::class);

        $transfer = new TransferObject([
            'first' => 'value'
        ]);
        $transfer->second;
    }

    public function testReturnCorrectAttribute()
    {
        $transfer = new TransferObject([
            'first' => 12.345
        ]);

        $this->assertEquals(
            12.345,
            $transfer->first
        );
    }
}
